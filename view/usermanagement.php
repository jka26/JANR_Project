<?php
//Database connection
include "../db/config.php";

//Fetch users
$users = []; // Initialize `$users` to an empty array
$sql = "SELECT user_id, first_name, last_name, email, age, location FROM profiles";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $users = $result->fetch_all(MYSQLI_ASSOC); // Populate `$users` with user data
}

$conn->close(); // Close the connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management | Rush!</title>
    <link rel="icon" type="image/x-con" href="../assets/favicon_rush.ico">
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
            <!--<button class="sidebar-btn">Messages</button>
            <button class="sidebar-btn">Profile</button>-->
            
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
                    <th>Email</th>
                    <th>Age</th>
                    <th>Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['user_id']) ?></td>
                    <td><?= htmlspecialchars($user['first_name']) ?></td>
                    <td><?= htmlspecialchars($user['last_name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['age']) ?></td>
                    <td><?= htmlspecialchars($user['location']) ?></td>
                    <td>
                        <a href="view_user.php?id=<?= $user['user_id'] ?>" class="btn"><i class="fas fa-eye"></i> View</a>
                        <a href="edit_user.php?id=<?= $user['user_id'] ?>" class="btn"><i class="fas fa-edit"></i> Edit</a>
                        <a href="users.php?delete_user_id=<?= $user['user_id'] ?>" class="btn" onclick="return confirm('Are you sure you want to delete this user?')"><i class="fas fa-trash"></i> Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>

                    
                    <!--<td>1</td>
                    <td>John Doe</td>
                    <td>28</td>
                    <td>Male</td>
                    <td>New York</td>
                    <td>
                        <div class="action-buttons">
                            <button>View</button>
                            <button>Edit</button>
                            <button>Delete</button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Jane Smith</td>
                    <td>26</td>
                    <td>Female</td>
                    <td>California</td>
                    <td>
                        <div class="action-buttons">
                            <button>View</button>
                            <button>Edit</button>
                            <button>Delete</button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Alex Johnson</td>
                    <td>30</td>
                    <td>Non-binary</td>
                    <td>Texas</td>
                    <td>
                        <div class="action-buttons">
                            <button>View</button>
                            <button>Edit</button>
                            <button>Delete</button>
                        </div>
                    </td>
                </tr>-->
            </tbody>
        </table>

        <div id="viewUserModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="document.getElementById('viewUserModal').style.display='none'">&times;</span>
                <h2>User Details</h2>
                <div id="viewDetails">
                    <!-- User details will be dynamically inserted here -->
                </div>
                <button onclick="document.getElementById('viewUserModal').style.display='none'">Close</button>
            </div>
        </div>

        <div id="editUserModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="document.getElementById('editUserModal').style.display='none'">&times;</span>
                <h2>Edit User</h2>
                <form id="editUserForm" method="put" action="editUsers.php">
                    <input type="hidden" id="editUserId">
                    <label for="editName">Name:</label>
                    <input type="text" id="editName" required>
                    <label for="editAge">Age:</label>
                    <input type="number" id="editAge" required>
                    <label for="editGender">Gender:</label>
                    <select id="editGender" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                    <label for="editLocation">Location:</label>
                    <input type="text" id="editLocation" required>
                    <button type="submit">Save Changes</button>
                </form>
            </div>
        </div>

    </main>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const modal = document.getElementById("addUserModal");
            const addUserForm = document.getElementById("addUserForm");
            const tableBody = document.querySelector("tbody");

            // View More Modal elements
            const viewModal = document.getElementById("viewUserModal");
            const viewDetailsContainer = document.getElementById("viewDetails");

            // Edit User Modal elements
            const editModal = document.getElementById("editUserModal");
            const editForm = document.getElementById("editUserForm");
            const editUserIdInput = document.getElementById("editUserId");
            const editNameInput = document.getElementById("editName");
            const editAgeInput = document.getElementById("editAge");
            const editGenderInput = document.getElementById("editGender");
            const editLocationInput = document.getElementById("editLocation");

            // Show Add User Modal
            document.querySelector(".sidebar-btn.active").onclick = () => {
                modal.style.display = "block";
            };

            // Hide Add User Modal
            document.querySelector(".close").onclick = () => {
                modal.style.display = "none";
            };

            // Add User
            addUserForm.onsubmit = function (e) {
                e.preventDefault();

                const name = document.getElementById("name").value;
                const age = document.getElementById("age").value;
                const gender = document.getElementById("gender").value;
                const location = document.getElementById("location").value;
                const email = document.getElementById("email").value;

                // Add new row dynamically
                const newRow = document.createElement("tr");
                const userId = Date.now(); // Unique ID for this example

                newRow.innerHTML = `
                    <td>${userId}</td>
                    <td>${name}</td>
                    <td>${age}</td>
                    <td>${gender}</td>
                    <td>${location}</td>
                    <td>
                        <a href="#" class="btn view-btn"><i class="fas fa-eye"></i> View</a>
                        <a href="#" class="btn edit-btn"><i class="fas fa-edit"></i> Edit</a>
                        <a href="#" class="btn delete-btn"><i class="fas fa-trash"></i> Delete</a>
                    </td>
                `;

                tableBody.appendChild(newRow);

                attachRowListeners(newRow); // Attach event listeners to the new row
                modal.style.display = "none"; // Close modal
                addUserForm.reset(); // Reset form
            };

            // Attach event listeners for rows
            function attachRowListeners(row) {
                // View button
                row.querySelector(".view-btn").onclick = () => {
                    const cells = Array.from(row.children);

                    // Populate the View Modal with user data
                    viewDetailsContainer.innerHTML = `
                        <p><strong>ID:</strong> ${cells[0].textContent}</p>
                        <p><strong>Name:</strong> ${cells[1].textContent}</p>
                        <p><strong>Age:</strong> ${cells[2].textContent}</p>
                        <p><strong>Gender:</strong> ${cells[3].textContent}</p>
                        <p><strong>Location:</strong> ${cells[4].textContent}</p>
                    `;

                    viewModal.style.display = "block";

                    // Fetch user details from view.php
                    fetch(`../functions/viewUser.php?id=${userId}`)
                        .then((response) => response.text())
                        .then((html) => {
                            // Load the fetched HTML into the View Modal
                            viewDetailsContainer.innerHTML = html;
                            viewModal.style.display = "block";
                        })
                        .catch((error) => console.error("Error fetching user details:", error));
                };

                    
            };

                // Edit button (use modal instead of prompt)
                row.querySelector(".edit-btn").onclick = () => {
                    const cells = Array.from(row.children);

                    // Populate the Edit Modal fields with user data
                    editUserIdInput.value = cells[0].textContent; // ID
                    editNameInput.value = cells[1].textContent; // Name
                    editAgeInput.value = cells[2].textContent; // Age
                    editGenderInput.value = cells[3].textContent; // Gender
                    editLocationInput.value = cells[4].textContent; // Location

                    editModal.style.display = "block";

                    // On Edit Modal form submission
                    editForm.onsubmit = function (e) {
                        e.preventDefault();

                        // Update table row with new values
                        cells[1].textContent = editNameInput.value;
                        cells[2].textContent = editAgeInput.value;
                        cells[3].textContent = editGenderInput.value;
                        cells[4].textContent = editLocationInput.value;

                        // Close Edit Modal
                        editModal.style.display = "none";
                    };
                };

                // Delete button
                row.querySelector(".delete-btn").onclick = () => {
                    if (confirm("Are you sure you want to delete this user?")) {
                        row.remove();
                    }
                };
            }

            // Attach listeners to existing rows
            document.querySelectorAll("tbody tr").forEach(attachRowListeners);

            // Close View Modal when clicking outside
            window.onclick = function (event) {
                if (event.target === viewModal) {
                    viewModal.style.display = "none";
                }
            };
        );

    </script>

</body>
</html>
