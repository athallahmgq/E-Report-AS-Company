function confirmDelete(page, query, identifier) {
  Swal.fire({
    title: "Are you sure?",
    text: "You won't be able to revert this!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it!",
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = `index.php?page=${page}&${query}=${identifier}`;
    }
  });
}

function confirmApprove(page, query, identifier) {
  Swal.fire({
    title: "Are you sure?",
    text: "Do you want to approve this report",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, approve it!",
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = `index.php?page=${page}&${query}=${identifier}`;
    }
  });
}
