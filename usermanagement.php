<?php
// Database connection
include "../db/config.php";

// Fetch users
$users = [];
$sql = "SELECT user_id, first_name, last_name, email, age, gender, location FROM profiles";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $users = $result->fetch_all(MYSQLI_ASSOC);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management | Rush!</title>
    <link rel="stylesheet" href="../assets/usermanagement.css">
</head>
<body>
    <nav class="sidebar">
        <div class="logo">
            <h1>Rush!</h1>
        </div>
        <div class="menu">
            <button class="sidebar-btn" onclick="location.href='dashboard.php'">Dashboard</button>
            <button class="sidebar-btn active" onclick="location.href='usermanagement.php'">Users</button>
            <button class="sidebar-btn">Logout</button>
        </div>
    </nav>
    <main class="main-content">
        <h1>User Management</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr data-id="<?= htmlspecialchars($user['user_id']) ?>">
                    <td><?= htmlspecialchars($user['user_id']) ?></td>
                    <td><?= htmlspecialchars($user['first_name']) ?></td>
                    <td><?= htmlspecialchars($user['last_name']) ?></td>
                    <td><?= htmlspecialchars($user['age']) ?></td>
                    <td><?= htmlspecialchars($user['gender']) ?></td>
                    <td><?= htmlspecialchars($user['location']) ?></td>
                    <td>
                        <button class="btn edit-btn">Edit</button>
                        <button class="btn delete-btn" data-id="<?= htmlspecialchars($user['user_id']) ?>">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModalButton">&times;</span>
            <h2>Edit User</h2>
            <form id="editUserForm">
                <input type="hidden" id="editUserId">
                <label for="editFirstName">First Name:</label>
                <input type="text" id="editFirstName" required><br>
                <label for="editLastName">Last Name:</label>
                <input type="text" id="editLastName" required><br>
                <label for="editAge">Age:</label>
                <input type="number" id="editAge" required><br>
                <label for="editGender">Gender:</label>
                <select id="editGender" required><br>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
                <label for="editLocation">Location:</label>
                <input type="text" id="editLocation" required><br>
                <button type="submit">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const editModal = document.getElementById("editUserModal");
            const editForm = document.getElementById("editUserForm");

            const closeModalButton = document.getElementById("closeModalButton"); // Close button reference


            const editUserIdInput = document.getElementById("editUserId");
            const editFirstNameInput = document.getElementById("editFirstName");
            const editLastNameInput = document.getElementById("editLastName");
            const editAgeInput = document.getElementById("editAge");
            const editGenderInput = document.getElementById("editGender");
            const editLocationInput = document.getElementById("editLocation");

            // Open the Edit Modal and populate data
            document.querySelectorAll(".edit-btn").forEach(button => {
                button.addEventListener("click", event => {
                    const row = event.target.closest("tr");
                    const userId = row.dataset.id;

                    editUserIdInput.value = userId;
                    editFirstNameInput.value = row.children[1].textContent.trim();
                    editLastNameInput.value = row.children[2].textContent.trim();
                    editAgeInput.value = row.children[3].textContent.trim();
                    editGenderInput.value = row.children[4].textContent.trim();
                    editLocationInput.value = row.children[5].textContent.trim();

                    editModal.style.display = "block";
                });
            });

            // Handle form submission for user editing
            editForm.addEventListener("submit", event => {
                event.preventDefault();

                const data = {
                    user_id: editUserIdInput.value,
                    fname: editFirstNameInput.value,
                    lname: editLastNameInput.value,
                    age: parseInt(editAgeInput.value, 10),
                    gender: editGenderInput.value,
                    location: editLocationInput.value
                };

                fetch('editUsers.php', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(response => {
                    if (response.message === "User details updated successfully") {
                        const row = document.querySelector(`tr[data-id="${data.user_id}"]`);
                        row.children[1].textContent = data.fname;
                        row.children[2].textContent = data.lname;
                        row.children[3].textContent = data.age;
                        row.children[4].textContent = data.gender;
                        row.children[5].textContent = data.location;

                        closeModal();
                        alert("User details updated successfully.");
                    } else {
                        alert(response.message || "An error occurred.");
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("An error occurred while updating user details.");
                });
            });

            // Function to close the modal
            const closeModal = () => {
                editModal.style.display = "none";
                document.getElementById("editUserForm").reset(); // Optional: Reset the form fields
            };

            // Add click event listener to the close button
            closeModalButton.addEventListener("click", closeModal);

            // (Optional) Close modal when clicking outside the modal content
            window.addEventListener("click", (event) => {
                if (event.target === editModal) {
                    closeModal();
                }
            });

            // Handle delete button click
            document.querySelectorAll(".delete-btn").forEach(button => {
                button.addEventListener("click", event => {
                    const userId = button.getAttribute("data-id");

                    if (confirm("Are you sure you want to delete this user?")) {
                        fetch("deleteUser.php", {
                            method: "DELETE",
                            headers: { "Content-Type": "application/json" },
                            body: JSON.stringify({ user_id: userId })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.message === "User deleted successfully") {
                                const row = button.closest("tr");
                                row.remove();
                                alert("User deleted successfully!");
                            } else {
                                alert(data.message || "An error occurred while deleting the user.");
                            }
                        })
                        .catch(error => {
                            console.error("Error deleting user:", error);
                            alert("An error occurred while deleting the user.");
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
