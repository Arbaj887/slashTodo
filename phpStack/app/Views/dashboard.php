<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp,container-queries"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css" integrity="sha512-9xKTRVabjVeZmc+GUW8GgSmcREDunMM+Dt/GrzchfN8tkwHizc5RP4Ok/MXFFy5rIjJjzhndFScTceq5e6GvVQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>DashBoard</title>
</head>

<body class="">
    <div class="sm:h-screen w-full flex justify-center items-center bg-slate-200  min-w-[720px] ">
        <div class="w-full h-[90%] sm:w-[90%] shadow-lg rounded-lg bg-white m-7 p-7 overflow-y-scroll text-center">
            <!-----------------------------------------Header--Section---------------------------------------------  -->
            <div class="flex flex-row justify-center md:justify-between items-center m-5 flex-wrap">
                <h1 class="text-2xl font-semibold m-5 text-center">User Dashboard</h1>
                <!-- ---------------------------------Seach--Bar--------------------------------------- -->
                <div class="flex flex-row justify-between items-center m-5 flex-wrap">
                    <input type="text" class=" rounded-lg border border-gray-400" placeholder="Search"
                        id="search"
                        onkeyup="searchUser()" />
                </div>
                <!-- ----------------------------------Logout--and--Download---------------------------------- -->
                <div class="flex flex-row justify-between items-center m-5 flex-wrap">
                    <a href="<?php echo base_url('/logout'); ?>" class="bg-green-400 text-white hover:bg-red-600 text-xl px-4 p-2 m-4 rounded-lg">Logout</a>
                    <button onclick="downloadData()">

                        <i class="fa-solid fa-download bg-blue-500 text-white hover:bg-red-600 text-xl px-4 p-2 m-4 rounded-lg"></i>
                    </button>
                </div>
            </div>
            <!-----------------------table-----------------------------------------------------------------------  -->
            <table class="min-w-full border-collapse border border-gray-300" id="userTable">
                <thead>
                    <tr class="bg-blue-600 text-white">
                        <th class="py-3 px-4 border-b border-gray-300">Id</th>
                        <th class="py-3 px-4 border-b border-gray-300">mongoId</th>
                        <th class="py-3 px-4 border-b border-gray-300">Name</th>
                        <th class="py-3 px-4 border-b border-gray-300">Email</th>
                        <th class="py-3 px-4 border-b border-gray-300">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    foreach ($users as $user) {

                    ?>
                        <tr class="hover:bg-gray-100 transition duration-200">
                            <td class="py-3 px-4 border-b border-gray-300 "><?php echo $user->id; ?></td>
                            <td class="py-3 px-4 border-b border-gray-300 "><?php echo $user->mongoId; ?></td>
                            <td class="py-3 px-4 border-b border-gray-300 "><?php echo $user->name; ?></td>
                            <td class="py-3 px-4 border-b border-gray-300 "><?php echo $user->email; ?></td>
                            <td class="py-3 px-4 border-b border-gray-300 text-center">
                                <!-------------------------------------Edit--User--------------------------------------------------------------------  -->

                                <button class="text-white bg-blue-500 hover:bg-blue-600 rounded-full p-2 mr-2 m-3 transition duration-200"
                                    onclick="openEdit(<?php echo $user->id; ?>,'<?php echo $user->name; ?>','<?php echo $user->email; ?>','<?php echo $user->mongoId; ?>')">
                                    <i class="fa-solid fa-pen-to-square text-lg">

                                    </i>
                                </button>





                                <!---------------------------------------Delete--User------------------------------------------------------------------  -->
                                <a href="<?php echo base_url('/deleteuser/' . $user->id . '/' . $user->mongoId); ?>">
                                    <button class="text-white bg-red-500 hover:bg-red-600 rounded-full p-2 m-3 transition duration-200">
                                        <i class="fa-solid fa-trash text-lg"></i>
                                    </button>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <!---------------- -----------------------------------Paggination----------------------------------------------------------------------------- -->
            <!---------------- Pagination -------------------->
            <div class="flex justify-center items-center w-full sm:w-[90%] bg-white m-7 p-7 text-center">
                <div class="flex flex-row justify-between items-center mb-7 w-full sm:w-[60%] ">
                    <!-- Back Button -->
                    <?php if ($currentPage > 1): ?>
                        <a href="?page=<?php echo $currentPage - 1; ?>" class="bg-blue-500 border-r-3 text-white px-5 py-2 rounded-lg">Back</a>
                    <?php endif; ?>
                    <?php if ($currentPage <= 1): ?>
                        <button class="bg-gray-500 border-r-3 text-white px-5 py-2 rounded-lg " disabled>Back</button>
                    <?php endif; ?>
                    <!-- Current Page -->
                    <div class="flex flex-row border-r-3 text-black px-5 py-3 rounded-lg overflow-auto m-5">
                        Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?>
                    </div>

                    <!-- Next Button -->
                    <?php if ($currentPage < $totalPages): ?>
                        <a href="?page=<?php echo $currentPage + 1; ?>" class="bg-blue-500 border-r-3 text-white px-5 py-2 rounded-lg">Next</a>
                    <?php endif; ?>
                    <?php if ($currentPage >= $totalPages): ?>
                        <button class="bg-gray-400 border-r-3 text-white px-5 py-2 rounded-lg" disabled>Next</button>
                    <?php endif; ?>
                </div>
            </div>


        </div>
        <!--------------------------------------------------------------------Edit--Section---------------------------------------------------------------  -->

        <div class="absolute w-full bg-gray-500 bg-opacity-50 flex items-center justify-center h-screen hidden" id="editPage">
            <div class=" bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
                <h1 class="text-3xl font-semibold text-center text-gray-800 mb-6">Edit</h1>


                <form action="<?= base_url("/updateUser") ?>" method="post">

                    <div class="mb-4">
                        <label for="editId" class="block text-gray-700 text-sm font-semibold mb-2">Id:</label>
                        <input type="text" id="editId" name="editId" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Enter your name" readonly>
                    </div>

                    <div class="mb-4 hidden">
                        <label for="editEmail" class="block text-gray-700 text-sm font-semibold mb-2">MongoId:</label>
                        <input type="text" id="mongoId" name="mongoId" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Enter your email" readonly>
                    </div>

                    <div class="mb-4">
                        <label for="editName" class="block text-gray-700 text-sm font-semibold mb-2">Name:</label>
                        <input type="text" id="editName" name="editName" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Enter your name" required>
                    </div>


                    <div class="mb-4">
                        <label for="editEmail" class="block text-gray-700 text-sm font-semibold mb-2">Email:</label>
                        <input type="email" id="editEmail" name="editEmail" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Enter your email" required>
                    </div>




                    <div>

                        <button type="submit" name="updateUser" id="updateUser" class=" m-2 w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            Update
                        </button>

                    </div>



                </form>

                <button class=" m-2 w-full bg-gray-400 text-white py-2 rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500"
                    onclick="closeEdit()">
                    Cancel
                </button>


            </div>
        </div>
    </div>

    <!-- -----------------------------------Script-------------------------------------------------------------------------- -->
    <script>
        function openEdit(id, name, email, mongoId) {

            document.getElementById("editName").value = name;
            document.getElementById("editEmail").value = email;
            document.getElementById("editId").value = id;
            document.getElementById("mongoId").value = mongoId;
            document.getElementById("editPage").classList.remove("hidden");


        }

        function closeEdit() {
            document.getElementById("editPage").classList.add("hidden");


        }


        function searchUser() {
            let search = document.getElementById("search").value.toLowerCase().trim();
            let users = document.getElementById("userTable");
            let usersList = users.getElementsByTagName("tr");

            for (let i = 1; i < usersList.length; i++) { 
                let cells = usersList[i].getElementsByTagName("td");
                if (cells.length > 0) { 
                    let name = cells[2].textContent.toLowerCase(); 
                    let email = cells[3].textContent.toLowerCase(); 

                    if (name.includes(search) || email.includes(search)) {
                        usersList[i].style.display = ""; 
                    } else {
                        usersList[i].style.display = "none"; 
                        
                    }
                }
            }
        }

        function downloadData() {
            let data = document.getElementById("userTable");
            let csvContent = "data:text/csv;charset=utf-8,";
            let rows = data.getElementsByTagName("tr");


            for (let i = 0; i < rows.length; i++) {
                let row = rows[i];
                let id = row.cells[0].textContent;
                let name = row.cells[1].textContent;
                let email = row.cells[2].textContent;
                let mongoId = row.cells[3].textContent;

                // Fetch additional data if necessary
                let url = `/api/users/${mongoId}`;
                fetch(url)
                    .then(response => response.json())
                    .then(userData => {
                        // Assuming userData contains the data you want to add to the CSV
                        let additionalData = userData.additionalField; // Adjust as necessary
                        let csvRow = [id, name, email, mongoId, additionalData].join(","); // Create CSV row
                        csvContent += csvRow + "\n"; // Append row to CSV content

                        // Check if this is the last row to trigger download
                        if (i === rows.length - 1) {
                            downloadCSV(csvContent);
                        }
                    })
                    .catch(error => console.error('Error fetching user data:', error));
            }
        }

        function downloadCSV(csvContent) {
            let encodedUri = encodeURI(csvContent);
            let link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "data.csv");
            document.body.appendChild(link); // Required for Firefox
            link.click(); // Trigger the download
            document.body.removeChild(link); // Clean up
        }
    </script>
</body>

</html>