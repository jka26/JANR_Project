// JavaScript for User Management

document.addEventListener("DOMContentLoaded", () => {
    const tableBody = document.querySelector("tbody");

    // View button functionality
    const viewButtons = document.querySelectorAll(".action-buttons button:nth-child(1)");
    viewButtons.forEach((button, index) => {
        button.addEventListener("click", () => {
            const row = button.closest("tr");
            const userData = Array.from(row.children).slice(0, 5).map(cell => cell.textContent);
            alert(`Viewing User Details:\nID: ${userData[0]}\nName: ${userData[1]}\nAge: ${userData[2]}\nGender: ${userData[3]}\nLocation: ${userData[4]}`);
        });
    });

    // Edit button functionality
    const editButtons = document.querySelectorAll(".action-buttons button:nth-child(2)");
    editButtons.forEach((button) => {
        button.addEventListener("click", () => {
            const row = button.closest("tr");
            const id = row.children[0].textContent;
            const name = prompt("Enter new name:", row.children[1].textContent);
            const age = prompt("Enter new age:", row.children[2].textContent);
            const gender = prompt("Enter new gender:", row.children[3].textContent);
            const location = prompt("Enter new location:", row.children[4].textContent);

            if (name && age && gender && location) {
                row.children[1].textContent = name;
                row.children[2].textContent = age;
                row.children[3].textContent = gender;
                row.children[4].textContent = location;
                alert(`User with ID ${id} has been updated successfully!`);
            } else {
                alert("All fields must be filled to edit user details.");
            }
        });
    });

    // Delete button functionality
    const deleteButtons = document.querySelectorAll(".action-buttons button:nth-child(3)");
    deleteButtons.forEach((button) => {
        button.addEventListener("click", () => {
            const row = button.closest("tr");
            const userName = row.children[1].textContent;

            if (confirm(`Are you sure you want to delete user: ${userName}?`)) {
                row.remove();
                alert(`User ${userName} has been deleted.`);
            }
        });
    });
});
