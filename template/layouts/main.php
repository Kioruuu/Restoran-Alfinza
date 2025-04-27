<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Restaurant Management System'; ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Theme -->
    <link rel="stylesheet" href="assets/css/theme.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gradient-to-br from-indigo-50 to-blue-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-xl">
            <?php include 'template/components/sidebar.php'; ?>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8 overflow-y-auto">
            <div class="max-w-7xl mx-auto fade-in">
                <?php 
                if(isset($content)) {
                    echo $content;
                }
                ?>
            </div>
        </main>
    </div>

    <!-- Footer -->
    <footer class="bg-white shadow-lg mt-auto py-4">
        <div class="max-w-7xl mx-auto px-4 text-center text-gray-600">
            <p>&copy; <?php echo date('Y'); ?> Restaurant Management System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html> 