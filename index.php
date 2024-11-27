<?php
ob_start();
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: /auth/login.php');
}

include_once($_SERVER['DOCUMENT_ROOT'] . "/utils/functions.php");

include_once($_SERVER['DOCUMENT_ROOT'] . "/layout/header.php");
if (!isset($_GET['page'])) {
    header("Location: /index.php?page=dashboard");
}

include_once($_SERVER['DOCUMENT_ROOT'] . "/layout/navbar.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/layout/sidebar.php");

?>

<link rel="stylesheet" href="hello.css">

<div class="span-hello-container">
          <span class="span-hello"></span>
        </div>
      </div>

<script>
    const hello_arr = ["A'S Company"];

window.onload = function () {
    const spanHelloContainer = document.querySelector(".span-hello-container");
    const spanHello = document.querySelector(".span-hello");

    let currentIndex = 0;

    function displayNextHello() {
        if (currentIndex < hello_arr.length) {
            spanHello.textContent = hello_arr[currentIndex];
            currentIndex++;
            setTimeout(displayNextHello, 1000);
        } else {
            spanHelloContainer.classList.add("translate-animation");
        }
    }

    displayNextHello();
};

</script>

<main class="flex flex-col gap-4 md:gap-5 px-4 lg:px-5 lg:ml-56 h-auto pt-20 pb-5">
    <?php
    if ($_GET['page'] == "" || $_GET["page"] == "dashboard") {
        if ($_SESSION['role_name'] !== 'masyarakat') {
            include_once("dashboard.php");
        } else {
            echo "<script>
                    Swal.fire({
                        title: 'Unauthorized',
                        text: 'You don\'t have access to this page',
                        icon: 'error'
                        }).then((result) => {
                            window.location.href = 'index.php?page=reports';
                        });
                    </script>";
        }
    }

    if ($_GET['page'] == "users") {
        if ($_SESSION['role_name'] === 'superadmin') {
            include_once("users.php");
        } else {
            echo "<script>
                Swal.fire({
                    title: 'Unauthorized',
                    text: 'You don\'t have access to this page',
                    icon: 'error'
                    }).then((result) => {
                        window.history.back();
                    });
                </script>";
        }
    }

    if ($_GET['page'] == "users_delete") {
        if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
            $user_id = $_GET['user_id'];

            if (hapus('id', 'users', $user_id) > 0) {
                echo "<script>
                Swal.fire({
                    title: 'Good job!',
                    text: 'Data berhasil dihapus',
                    icon: 'success'
                    }).then((result) => {
                        window.location.href = 'index.php?page=users';
                    });
                </script>";
            } else {
                echo "<script>
                Swal.fire({
                    title: 'Failed!',
                    text: 'Data gagal dihapus',
                    icon: 'error'
                    }).then((result) => {
                        window.location.href = 'index.php?page=users';
                    });
                </script>";
            }
        } else {
            header("Location: /index.php?page=users");
            exit();
        }
    }

    if ($_GET['page'] == "list") {
        include_once("list.php");
    }

    if ($_GET['page'] == "reports") {
        include_once("reports.php");
    }

    if ($_GET['page'] == "reports_delete") {
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $id = $_GET['id'];

            if (hapus('id', 'reports', $id) > 0) {
                echo "<script>
                Swal.fire({
                    title: 'Good job!',
                    text: 'Data berhasil dihapus',
                    icon: 'success'
                    }).then((result) => {
                        window.location.href = 'index.php?page=list';
                    });
                </script>";
            } else {
                echo "<script>
                Swal.fire({
                    title: 'Failed!',
                    text: 'Data gagal dihapus',
                    icon: 'error'
                    }).then((result) => {
                        window.location.href = 'index.php?page=list';
                    });
                </script>";
            }
        } else {
            header("Location: /index.php?page=list");
            exit();
        }
    }

    if ($_GET['page'] == "reports_approve") {
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $id = $_GET['id'];

            approve('reports', $id);
            echo "<script>
                Swal.fire({
                    title: 'Good job!',
                    text: 'Report has been approved successfully',
                    icon: 'success'
                    }).then((result) => {
                        window.location.href = 'index.php?page=list';
                    });
                </script>";
        } else {
            header("Location: /index.php?page=list");
            exit();
        }
    }

    if ($_GET['page'] == "create") {
        if ($_SESSION['role_name'] === 'masyarakat') {
            include_once("create-report.php");
        } else {
            echo "<script>
                Swal.fire({
                    title: 'Unauthorized',
                    text: 'You don\'t have access to this page',
                    icon: 'error'
                    }).then((result) => {
                        window.history.back();
                    });
                </script>";
        }
    }

    if ($_GET['page'] == "detail" && isset($_GET["id"])) {
        include_once("detail.php");
    }

    if ($_GET['page'] == "logout") {
        include_once($_SERVER['DOCUMENT_ROOT'] . "/utils/auth.php");
    }


    ?>
</main>

<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/layout/footer.php");
?>