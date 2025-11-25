<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LineIcons Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold mb-8 text-gray-900">LineIcons Test Page</h1>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <!-- Test navigation icons -->
            <div class="text-center p-4 bg-gray-50 rounded">
                <x-lineicon alias="navigation.dashboard" class="mx-auto text-blue-600" />
                <p class="mt-2 text-sm">Dashboard</p>
            </div>
            
            <div class="text-center p-4 bg-gray-50 rounded">
                <x-lineicon alias="navigation.calendar" class="mx-auto text-blue-600" />
                <p class="mt-2 text-sm">Calendar</p>
            </div>
            
            <div class="text-center p-4 bg-gray-50 rounded">
                <x-lineicon alias="navigation.content" class="mx-auto text-blue-600" />
                <p class="mt-2 text-sm">Content</p>
            </div>
            
            <div class="text-center p-4 bg-gray-50 rounded">
                <x-lineicon alias="navigation.people" class="mx-auto text-blue-600" />
                <p class="mt-2 text-sm">People</p>
            </div>
            
            <!-- Test action icons -->
            <div class="text-center p-4 bg-gray-50 rounded">
                <x-lineicon alias="actions.edit" class="mx-auto text-green-600" />
                <p class="mt-2 text-sm">Edit</p>
            </div>
            
            <div class="text-center p-4 bg-gray-50 rounded">
                <x-lineicon alias="actions.delete" class="mx-auto text-red-600" />
                <p class="mt-2 text-sm">Delete</p>
            </div>
            
            <div class="text-center p-4 bg-gray-50 rounded">
                <x-lineicon alias="actions.view" class="mx-auto text-blue-600" />
                <p class="mt-2 text-sm">View</p>
            </div>
            
            <div class="text-center p-4 bg-gray-50 rounded">
                <x-lineicon alias="actions.add" class="mx-auto text-green-600" />
                <p class="mt-2 text-sm">Add</p>
            </div>
            
            <!-- Test content type icons -->
            <div class="text-center p-4 bg-gray-50 rounded">
                <x-lineicon alias="content_types.video" class="mx-auto text-purple-600" />
                <p class="mt-2 text-sm">Video</p>
            </div>
            
            <div class="text-center p-4 bg-gray-50 rounded">
                <x-lineicon alias="content_types.audio" class="mx-auto text-purple-600" />
                <p class="mt-2 text-sm">Audio</p>
            </div>
            
            <div class="text-center p-4 bg-gray-50 rounded">
                <x-lineicon alias="content_types.image" class="mx-auto text-purple-600" />
                <p class="mt-2 text-sm">Image</p>
            </div>
            
            <div class="text-center p-4 bg-gray-50 rounded">
                <x-lineicon alias="content_types.document" class="mx-auto text-purple-600" />
                <p class="mt-2 text-sm">Document</p>
            </div>
        </div>
        
        <div class="mt-8 p-4 bg-blue-50 rounded">
            <h2 class="font-bold text-lg mb-2">Test Status</h2>
            <p class="text-sm text-gray-700">If you see icons above, LineIcons are working correctly!</p>
            <p class="text-sm text-gray-700 mt-2">Icons are loaded from: <code class="bg-white px-2 py-1 rounded">public/vendor/lineicons/</code></p>
        </div>
    </div>
</body>
</html>
