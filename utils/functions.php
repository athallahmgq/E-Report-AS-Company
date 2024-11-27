<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/utils/connection.php");

function query($query)
{
    global $connection;
    $result = mysqli_query($connection, $query);

    $rows = [];
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }

    return $rows;
}

function tambah($data, $table, $fields)
{

    global $connection;

    $columns = [];
    $values = [];

    foreach ($fields as $field) {
        if (isset($data[$field])) {
            if ($field === 'password') {
                $hashedPassword = password_hash($data[$field], PASSWORD_DEFAULT);
                $columns[] = $field;
                $values[] = "'" . $hashedPassword . "'";
            } else {
                $columns[] = $field;

                $values[] = "'" . htmlspecialchars($data[$field]) . "'";
            }
        }
    }

    if (isset($data['user_id'])) {
        $columns[] = 'user_id';
        $values[] = "'" . $data['user_id'] . "'";
    }

    if (isset($data['status'])) {
        $columns[] = 'status';
        $values[] = "'" . $data['status'] . "'";
    }

    if (isset($data['slug'])) {
        $columns[] = 'slug';
        $values[] = "'" . $data['slug'] . "'";
    }


    if (isset($data['nik'])) {
        $nik = htmlspecialchars($data["nik"]);
        $checkNIK = query("SELECT * FROM users WHERE nik='$nik'");

        if (count($checkNIK) > 0) {
            return -1;
        }
    }

    if (isset($_FILES['thumbnail'])) {
        $image = upload();
        if ($image === false) {
            return false;
        }
        $columns[] = 'thumbnail';
        $values[] = "'" . $image . "'";
    }


    $query = "INSERT INTO $table (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $values) . ")";
    $insert = mysqli_query($connection, $query);
    return mysqli_affected_rows($connection);
}

function update($table, $data, $where)
{
    global $connection;

    if (isset($data['id'])) {
        unset($data['id']);
        unset($data['update']);
    }

    $fields = [];
    foreach ($data as $column => $value) {
        if ($column === 'password') {
            $value = password_hash($value, PASSWORD_DEFAULT);
        } elseif ($column === 'thumbnail' && $_FILES['thumbnail']['error'] !== 4) {
            $image = upload();
            if (!$image) {
                return false;
            }
            if (!empty($data['old_image'])) {
                unlink('./img/' . $data['old_image']);
            }
            $value = $image;
        } else {
            $value = htmlspecialchars($value);
        }

        $fields[] = "$column = '$value'";
    }

    $fields_sql = implode(", ", $fields);


    $where_sql = [];
    foreach ($where as $key => $val) {
        $val = htmlspecialchars($val);
        $where_sql[] = "$key = '$val'";
    }
    $where_clause = implode(" AND ", $where_sql);

    $query = "UPDATE $table SET $fields_sql WHERE $where_clause";

    mysqli_query($connection, $query);

    return mysqli_affected_rows($connection);
}


function hapus($identifier, $table, $id)
{
    global $connection;
    $id = htmlspecialchars($id);
    $data = query("SELECT * FROM reports WHERE id='$id'");
    if (!isset($data)) {
        return -1;
    }
    if (isset($data[0]['thumbnail'])) {
        unlink("../assets/upload/" . $data['thumbnail']);
    }
    mysqli_query($connection, "DELETE FROM $table WHERE $identifier='$id'");
    return mysqli_affected_rows($connection);
}

function approve($table, $id)
{
    global $connection;
    $id = htmlspecialchars($id);

    $data = query("SELECT * FROM reports WHERE id='$id'");
    if (!isset($data)) {
        return -1;
    }

    update($table, ['status' => 1], ['id' => $id]);
    return mysqli_affected_rows($connection);
}

function validateInput($data, $fields, $isUpdate = false)
{
    $errors = [];
    $valid = true;

    foreach ($fields as $field => $errorMessage) {
        if (empty($data[$field]) && !in_array($field, ['password', 'password_confirm', 'old_password'])) {
            $errors[$field] = $errorMessage;
            $valid = false;
        } else {
            $data[$field] = htmlspecialchars($data[$field]);
        }
    }

    if (!empty($data['password']) || !empty($data['password_confirm'])) {
        if ($data['password'] !== $data['password_confirm']) {
            $errors['password_confirm'] = 'Password did not match';
            $valid = false;
        }
    }

    if ($isUpdate && !empty($data['old_password'])) {
        global $connection;
        $userId = $data['id'];
        $query = "SELECT password FROM users WHERE id='$userId'";
        $result = mysqli_query($connection, $query);
        $user = mysqli_fetch_assoc($result);

        if (!password_verify($data['old_password'], $user['password'])) {
            $errors['old_password'] = 'Old password is incorrect';
            $valid = false;
        }
    }

    return ['valid' => $valid, 'errors' => $errors, 'data' => $data];
}

function handleFormSubmit($data, $table, $action)
{
    // Define field requirements per table
    $tableFields = [
        'users' => [
            'fields' => [
                'nik' => 'NIK is required',
                'username' => 'Username is required',
                'name' => 'Name is required',
                'phone_number' => 'Phone Number is required',
                'password' => 'Password is required',
                'password_confirm' => 'Confirmation password is required',
                'role_id' => 'Role must be selected',
                'old_password' => 'Old password is required'
            ],
            'successMessage' => 'User updated successfully',
            'duplicateMessage' => 'NIK already exists',
            'failureMessage' => 'User updated failed'
        ],
        'reports' => [
            'fields' => [
                'title' => 'Title is required',
                'content' => 'Content is required'
            ],
            'successMessage' => 'Report updated successfully',
            'failureMessage' => 'Report updated failed'
        ]
    ];

    if ($table === 'reports') {
        $data['status'] = 0;
        $data['user_id'] = $_SESSION['user_id'];
        $data['slug'] = strtolower(str_replace(" ", "-", $data['title']));
    }

    $fields = $tableFields[$table]['fields'];

    $isUpdate = ($action === 'update');

    if (!$isUpdate && isset($fields['old_password'])) {
        unset($fields['old_password']);
    }

    $validationResult = validateInput($data, $fields, $isUpdate);

    $valid = $validationResult['valid'];
    $errors = $validationResult['errors'];
    $data = $validationResult['data'];
    // Clean up password fields for update if not provided
    if (empty($data['password']) && $isUpdate) {
        unset($data['password'], $data['password_confirm'], $data['old_password']);
    } else {
        unset($data['password_confirm'], $data['old_password']);
    }

    if ($valid) {
        $result = $isUpdate ? update($table, $data, ['id' => $data['id']]) : tambah($data, $table, array_keys($fields));

        $successMessage = $tableFields[$table]['successMessage'];
        $duplicateMessage = $tableFields[$table]['duplicateMessage'] ?? 'Duplicate entry';
        $failureMessage = $tableFields[$table]['failureMessage'];

        if ($result > 0) {
            echo "<script>
            Swal.fire({
                title: 'Success',
                text: '$successMessage',
                icon: 'success'
            }).then((result) => {
                window.location.href = 'index.php?page=$table';
            });
            </script>";
        } else if ($result == -1) {
            echo "<script>
            Swal.fire({
                title: 'Failed',
                text: '$duplicateMessage',
                icon: 'error'
            }).then((result) => {
                window.location.href = 'index.php?page=$table';
            });
            </script>";
        } else {
            echo "<script>
            Swal.fire({
                title: 'Failed',
                text: '$failureMessage',
                icon: 'error'
            }).then((result) => {
                window.location.href = 'index.php?page=$table';
            });
            </script>";
        }
    } else {
        $errorMessage = implode(", ", array_values($errors));
        echo "<script>
        Swal.fire({
            title: 'Failed',
            text: '$errorMessage',
            icon: 'error'
        });
        </script>";
    }
}

function upload()
{
    $originalName = $_FILES['thumbnail']['name'];
    $filesize = $_FILES['thumbnail']['size'];
    $error = $_FILES['thumbnail']['error'];
    $tmpName = $_FILES['thumbnail']['tmp_name'];

    if (
        $error === 4
    ) {
        echo "<script>
				alert('pilih gambar terlebih dahulu!');
			  </script>";
        return false;
    }

    $validExtension = ['jpg', 'jpeg', 'png'];
    $ekstensiGambar = explode('.', $originalName);
    $ekstensiGambar = strtolower(end($ekstensiGambar));
    if (!in_array($ekstensiGambar, $validExtension)) {
        echo "<script>
				alert('File bukan gambar');
			  </script>";
        return false;
    }

    if ($filesize > 1000000) {
        echo "<script>
				alert('Ukuran gambar terlalu besar!');
			  </script>";
        return false;
    }

    $imgFolder = $_SERVER['DOCUMENT_ROOT'] . '/assets/upload/';

    if (!is_dir($imgFolder)) {
        mkdir($imgFolder, 0755, true);
    }
    $newFilename = uniqid() . '.' . $ekstensiGambar;
    move_uploaded_file($tmpName, $imgFolder . $newFilename);

    return $newFilename;
}

function timeAgo($timestamp)
{
    $time = strtotime($timestamp);
    $timeDifference = time() - $time;

    if ($timeDifference < 0) {
        return 'just now';
    }

    if ($timeDifference < 60) {
        return $timeDifference . ' seconds ago';
    } elseif ($timeDifference < 3600) {
        return floor($timeDifference / 60) . ' minutes ago';
    } elseif ($timeDifference < 86400) {
        return floor($timeDifference / 3600) . ' hours ago';
    } elseif ($timeDifference < 2592000) {
        return floor($timeDifference / 86400) . ' days ago';
    } elseif ($timeDifference < 31536000) {
        return floor($timeDifference / 2592000) . ' months ago';
    } else {
        return floor($timeDifference / 31536000) . ' years ago';
    }
}
