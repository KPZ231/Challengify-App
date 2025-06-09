<?php require_once __DIR__ . '/../admin/partials/header.php'; ?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">
            <?= $action === 'create' ? 'Create New Challenge' : 'Edit Challenge' ?>
        </h1>
        <a href="/admin/challenges" class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-md flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to Challenges
        </a>
    </div>
    
    <?php if (isset($errors)): ?>
        <div class="mb-4 p-4 rounded-md bg-red-100 text-red-700">
            <div class="font-bold">Please correct the following errors:</div>
            <ul class="mt-2 list-disc list-inside">
                <?php foreach ($errors as $field => $message): ?>
                    <li><?= htmlspecialchars($message) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <div class="bg-white p-6 rounded-lg shadow-md">
        <form method="POST" enctype="multipart/form-data" action="<?= $action === 'create' ? '/admin/challenges/create' : '/admin/challenges/update/' . $challenge['id'] ?>" class="space-y-8">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <!-- Basic Information Section -->
            <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                
                <div class="space-y-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                        <input type="text" id="title" name="title" value="<?= $action === 'edit' ? htmlspecialchars($challenge['title']) : (isset($old['title']) ? htmlspecialchars($old['title']) : '') ?>" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-150">
                    </div>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                        <textarea id="description" name="description" rows="6" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-150"><?= $action === 'edit' ? htmlspecialchars($challenge['description']) : (isset($old['description']) ? htmlspecialchars($old['description']) : '') ?></textarea>
                    </div>
                </div>
            </div>
            
            <!-- Challenge Settings Section -->
            <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Challenge Settings</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="difficulty" class="block text-sm font-medium text-gray-700 mb-1">Difficulty Level</label>
                        <select id="difficulty" name="difficulty" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-150">
                            <option value="easy" <?= ($action === 'edit' && $challenge['difficulty'] === 'easy') || (isset($old['difficulty']) && $old['difficulty'] === 'easy') ? 'selected' : '' ?>>Easy</option>
                            <option value="medium" <?= ($action === 'edit' && $challenge['difficulty'] === 'medium') || (isset($old['difficulty']) && $old['difficulty'] === 'medium') || ($action === 'create' && !isset($old['difficulty'])) ? 'selected' : '' ?>>Medium</option>
                            <option value="hard" <?= ($action === 'edit' && $challenge['difficulty'] === 'hard') || (isset($old['difficulty']) && $old['difficulty'] === 'hard') ? 'selected' : '' ?>>Hard</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select id="category_id" name="category_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-150">
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= htmlspecialchars($category['id']) ?>" 
                                    <?= ($action === 'edit' && $challenge['category_id'] === $category['id']) || 
                                        (isset($old['category_id']) && $old['category_id'] === $category['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="status" name="status" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-150">
                            <option value="draft" <?= ($action === 'edit' && $challenge['status'] === 'draft') || (isset($old['status']) && $old['status'] === 'draft') || ($action === 'create' && !isset($old['status'])) ? 'selected' : '' ?>>Draft</option>
                            <option value="active" <?= ($action === 'edit' && $challenge['status'] === 'active') || (isset($old['status']) && $old['status'] === 'active') ? 'selected' : '' ?>>Active</option>
                            <option value="completed" <?= ($action === 'edit' && $challenge['status'] === 'completed') || (isset($old['status']) && $old['status'] === 'completed') ? 'selected' : '' ?>>Completed</option>
                            <option value="cancelled" <?= ($action === 'edit' && $challenge['status'] === 'cancelled') || (isset($old['status']) && $old['status'] === 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Dates Section -->
            <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Challenge Dates</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="datetime-local" id="start_date" name="start_date" value="<?= $action === 'edit' ? str_replace(' ', 'T', substr($challenge['start_date'], 0, 16)) : (isset($old['start_date']) ? str_replace(' ', 'T', substr($old['start_date'], 0, 16)) : date('Y-m-d\TH:i')) ?>" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-150">
                    </div>
                    
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="datetime-local" id="end_date" name="end_date" value="<?= $action === 'edit' ? str_replace(' ', 'T', substr($challenge['end_date'], 0, 16)) : (isset($old['end_date']) ? str_replace(' ', 'T', substr($old['end_date'], 0, 16)) : date('Y-m-d\TH:i', strtotime('+7 days'))) ?>" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-150">
                    </div>
                </div>
            </div>
            
            <!-- Guidelines Section -->
            <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Guidelines & Rules</h3>
                
                <div class="space-y-6">
                    <div>
                        <label for="rules" class="block text-sm font-medium text-gray-700 mb-1">Rules</label>
                        <textarea id="rules" name="rules" rows="4" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-150"><?= $action === 'edit' ? htmlspecialchars($challenge['rules']) : (isset($old['rules']) ? htmlspecialchars($old['rules']) : '') ?></textarea>
                        <p class="mt-1 text-sm text-gray-500">Define clear rules that participants must follow.</p>
                    </div>
                    
                    <div>
                        <label for="submission_guidelines" class="block text-sm font-medium text-gray-700 mb-1">Submission Guidelines</label>
                        <textarea id="submission_guidelines" name="submission_guidelines" rows="4" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-150"><?= $action === 'edit' ? htmlspecialchars($challenge['submission_guidelines']) : (isset($old['submission_guidelines']) ? htmlspecialchars($old['submission_guidelines']) : '') ?></textarea>
                        <p class="mt-1 text-sm text-gray-500">Provide detailed instructions for submitting challenge entries.</p>
                    </div>
                </div>
            </div>
            
            <!-- Media Section -->
            <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Challenge Image</h3>
                
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Challenge Image</label>
                    <input type="file" id="image" name="image" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <?php if ($action === 'edit' && $challenge['image']): ?>
                        <div class="mt-2 flex items-center">
                            <span class="text-sm text-gray-500">Current image:</span>
                            <img src="/uploads/challenges/<?= htmlspecialchars($challenge['image']) ?>" alt="Challenge Image" class="ml-2 h-12 w-12 object-cover rounded-md">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-md">
                    <?= $action === 'create' ? 'Create Challenge' : 'Update Challenge' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../admin/partials/footer.php'; ?> 