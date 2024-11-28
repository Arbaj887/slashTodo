<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp,container-queries"></script>
    <title>Sign Up</title>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

    
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h1 class="text-3xl font-semibold text-center text-gray-800 mb-6">Sign Up</h1>
        
        <!-- Form Start -->
        <form action="<?= base_url("/signup") ?>" method="post">
            
            <!-- Name Field -->
            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-semibold mb-2">Name:</label>
                <input type="text" id="name" name="name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Enter your name" required>
            </div>

            <!-- Email Field -->
            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">Email:</label>
                <input type="email" id="email" name="email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Enter your email" required>
            </div>

            <!-- Password Field -->
            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Password:</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Enter your password" required>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                Sign Up
            </button>

            <!-- Login Link -->
            <p class="text-center text-sm text-gray-600 mt-4">
                Already have an account? <a href="<?= base_url('/login') ?>" class="text-indigo-600 hover:text-indigo-700">Login here</a>
            </p>
        </form>
        <!-- Form End -->
    </div>
    <!-- Signup Form Container End -->

     <!-- ---------------------------------------------Pop-Message---------------------------------------------- -->
     <?php if(session()->getFlashdata('popMessage') !== NULL){
    $filePath =__DIR__ . '/../Views/popMessage.php';
    if (file_exists($filePath)) {
        include_once($filePath);
    } else {
        echo "File not found: $filePath";
    }
} ?>
</body>
</html>