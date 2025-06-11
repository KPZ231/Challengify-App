<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($challenge->getTitle()) ?> - Participate in this creative challenge on Challengify">
    <meta name="keywords" content="challenge, creative challenge, <?= e($challenge->getTitle()) ?>, creativity, community challenge">
    <title><?= e($challenge->getTitle()) ?> | Challengify</title>
    <link rel="stylesheet" href="/css/tailwind/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>

    <!-- Challenge Header -->
    <header class="ch-bg-gradient text-white py-12">
        <div class="container mx-auto px-4">
            <div class="flex flex-wrap items-center justify-between">
                <div class="w-full lg:w-2/3 mb-6 lg:mb-0">
                    <h1 class="text-3xl md:text-4xl font-bold mb-4"><?= e($challenge->getTitle()) ?></h1>
                    <div class="flex flex-wrap gap-3 mb-4">
                        <span class="ch-badge <?= $challenge->getDifficulty() === 'easy' ? 'bg-green-100 text-green-600' : ($challenge->getDifficulty() === 'medium' ? 'bg-yellow-100 text-yellow-600' : 'bg-red-100 text-red-600') ?>">
                            <?= ucfirst(e($challenge->getDifficulty())) ?>
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-6 text-sm">
                        <div>
                            <i class="far fa-calendar-alt mr-1"></i>
                            <span>Start: <?= formatDate($challenge->getStartDate()->format('Y-m-d H:i:s'), 'M j, Y') ?></span>
                        </div>
                        <div>
                            <i class="far fa-calendar-check mr-1"></i>
                            <span>End: <?= formatDate($challenge->getEndDate()->format('Y-m-d H:i:s'), 'M j, Y') ?></span>
                        </div>
                        <div>
                            <i class="far fa-clock mr-1"></i>
                            <span>
                                <?php
                                $now = new DateTime();
                                $end = $challenge->getEndDate();
                                $diff = $now->diff($end);
                                
                                if ($end < $now) {
                                    echo 'Challenge ended';
                                } elseif ($diff->days > 0) {
                                    echo $diff->days . ' day' . ($diff->days > 1 ? 's' : '') . ' remaining';
                                } else {
                                    $hours = $diff->h;
                                    echo $hours . ' hour' . ($hours > 1 ? 's' : '') . ' remaining';
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <?php if ($challenge->getImage()): ?>
                <div class="w-full lg:w-1/3 lg:text-right">
                    <img src="<?= e($challenge->getImage()) ?>" alt="<?= e($challenge->getTitle()) ?>" class="rounded-lg shadow-lg inline-block max-h-48 object-cover">
                </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Flash Messages -->
        <?php if (hasFlash('success')): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p><?= getFlash('success') ?></p>
            </div>
        <?php endif; ?>
        
        <?php if (hasFlash('error')): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p><?= getFlash('error') ?></p>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Challenge Details -->
            <div class="lg:col-span-2">
                <div class="ch-card shadow-md mb-8">
                    <div class="p-6">
                        <h2 class="text-2xl font-semibold mb-4">Challenge Description</h2>
                        <div class="prose max-w-none">
                            <?= nl2br(e($challenge->getDescription())) ?>
                        </div>
                    </div>
                </div>
                
                <?php if ($challenge->getRules()): ?>
                <div class="ch-card shadow-md mb-8">
                    <div class="p-6">
                        <h2 class="text-2xl font-semibold mb-4">Rules</h2>
                        <div class="prose max-w-none">
                            <?= nl2br(e($challenge->getRules())) ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($challenge->getSubmissionGuidelines()): ?>
                <div class="ch-card shadow-md mb-8">
                    <div class="p-6">
                        <h2 class="text-2xl font-semibold mb-4">Submission Guidelines</h2>
                        <div class="prose max-w-none">
                            <?= nl2br(e($challenge->getSubmissionGuidelines())) ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Submissions Section -->
                <div class="mb-8">
                    <h2 class="text-2xl font-semibold mb-4">Community Submissions</h2>
                    
                    <?php if (empty($submissions)): ?>
                        <div class="bg-blue-50 p-6 rounded-lg text-center">
                            <h3 class="text-xl font-semibold mb-2">No submissions yet</h3>
                            <p>Be the first to submit your work for this challenge!</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-6">
                            <?php foreach ($submissions as $submission): ?>
                                <div class="ch-card shadow-md">
                                    <div class="p-6">
                                        <div class="flex justify-between items-start mb-4">
                                            <h3 class="text-xl font-semibold"><?= e($submission->getTitle()) ?></h3>
                                            <span class="text-sm text-gray-500"><?= timeAgo($submission->getCreatedAt()->format('Y-m-d H:i:s')) ?></span>
                                        </div>
                                        
                                        <p class="text-gray-600 mb-4"><?= nl2br(e($submission->getDescription())) ?></p>
                                        
                                        <?php if ($submission->getContent()): ?>
                                            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                                <div class="prose max-w-none">
                                                    <?= nl2br(e($submission->getContent())) ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($submission->getFilePath()): ?>
                                            <div class="mb-4">
                                                <?php
                                                $fileExt = pathinfo($submission->getFilePath(), PATHINFO_EXTENSION);
                                                $isImage = in_array(strtolower($fileExt), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                ?>
                                                
                                                <?php if ($isImage): ?>
                                                    <img src="<?= e($submission->getFilePath()) ?>" alt="Submission image" class="max-h-96 rounded-lg">
                                                <?php else: ?>
                                                    <a href="<?= e($submission->getFilePath()) ?>" class="ch-btn ch-btn-outline-primary" target="_blank">
                                                        <i class="fas fa-download mr-2"></i>Download Attachment
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <img src="/images/default-avatar.png" alt="User avatar" class="w-8 h-8 rounded-full mr-2">
                                                <span class="font-medium">
                                                    <?php
                                                    $user = \Kpzsproductions\Challengify\Models\User::find($submission->getUserId());
                                                    echo $user ? e($user->getUsername()) : 'Anonymous';
                                                    ?>
                                                </span>
                                            </div>
                                            
                                            <div class="flex items-center">
                                                <button class="vote-btn flex items-center text-gray-500 hover:text-blue-600" data-submission="<?= $submission->getId() ?>" data-vote="upvote">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z" />
                                                    </svg>
                                                    <span class="vote-count">
                                                        <?= \Kpzsproductions\Challengify\Models\Vote::countVotesBySubmission($submission->getId(), 'upvote') ?>
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Submit Entry -->
                <div class="ch-card shadow-md mb-8">
                    <div class="p-6">
                        <h2 class="text-2xl font-semibold mb-4">Submit Your Entry</h2>
                        
                        <?php if (!isLoggedIn()): ?>
                            <div class="bg-blue-50 p-4 rounded-lg mb-4">
                                <p class="mb-3">You need to be logged in to submit an entry.</p>
                                <a href="/login" class="ch-btn ch-btn-primary w-full">Login to Submit</a>
                            </div>
                        <?php elseif ($challenge->getStatus() !== 'active' || $challenge->getEndDate() < new DateTime()): ?>
                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <p>This challenge is no longer accepting submissions.</p>
                            </div>
                        <?php elseif ($userSubmission): ?>
                            <div class="bg-green-50 p-4 rounded-lg mb-4">
                                <h3 class="font-semibold mb-2">You've already submitted an entry</h3>
                                <p>You submitted: <strong><?= e($userSubmission->getTitle()) ?></strong></p>
                                <p class="text-sm text-gray-600 mt-2">Submitted <?= timeAgo($userSubmission->getCreatedAt()->format('Y-m-d H:i:s')) ?></p>
                            </div>
                            <button id="edit-submission-btn" class="ch-btn ch-btn-outline-primary w-full">Edit My Submission</button>
                        <?php else: ?>
                            <form action="/challenges/<?= $challenge->getId() ?>/submit" method="POST" enctype="multipart/form-data">
                                <!-- CSRF Protection -->
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                
                                <div class="mb-4">
                                    <label for="title" class="block text-gray-700 font-medium mb-2">Title</label>
                                    <input type="text" id="title" name="title" class="form-input w-full rounded-md" value="<?= isset($old['title']) ? e($old['title']) : '' ?>" required>
                                    <?php if (isset($errors['title'])): ?>
                                        <p class="text-red-500 text-sm mt-1"><?= $errors['title'] ?></p>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="description" class="block text-gray-700 font-medium mb-2">Description</label>
                                    <textarea id="description" name="description" rows="3" class="form-textarea w-full rounded-md" required><?= isset($old['description']) ? e($old['description']) : '' ?></textarea>
                                    <?php if (isset($errors['description'])): ?>
                                        <p class="text-red-500 text-sm mt-1"><?= $errors['description'] ?></p>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="content" class="block text-gray-700 font-medium mb-2">Content</label>
                                    <textarea id="content" name="content" rows="5" class="form-textarea w-full rounded-md" required><?= isset($old['content']) ? e($old['content']) : '' ?></textarea>
                                    <?php if (isset($errors['content'])): ?>
                                        <p class="text-red-500 text-sm mt-1"><?= $errors['content'] ?></p>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="mb-6">
                                    <label for="submission_file" class="block text-gray-700 font-medium mb-2">Attachment (optional)</label>
                                    <input type="file" id="submission_file" name="submission_file" class="form-input w-full">
                                    <p class="text-sm text-gray-500 mt-1">Supported formats:</p>
                                    <ul class="text-sm text-gray-500 list-disc ml-5 mt-1">
                                        <li>Images: JPG, PNG, GIF, WebP</li>
                                        <li>Documents: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, CSV, HTML</li>
                                        <li>Videos: MP4, MPEG, MOV, AVI, WMV, WebM</li>
                                    </ul>
                                    <p class="text-sm text-gray-500 mt-1">Maximum file size: 15MB</p>
                                    <?php if (isset($errors['file'])): ?>
                                        <p class="text-red-500 text-sm mt-1"><?= $errors['file'] ?></p>
                                    <?php endif; ?>
                                </div>
                                
                                <button type="submit" class="ch-btn ch-btn-primary w-full">Submit Entry</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Challenge Info -->
                <div class="ch-card shadow-md">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Challenge Info</h3>
                        <ul class="space-y-3">
                            <li class="flex items-start">
                                <i class="fas fa-user-circle mt-1 mr-3 text-blue-500"></i>
                                <div>
                                    <span class="block text-gray-600">Created by</span>
                                    <span class="font-medium">
                                        <?php
                                        $creator = \Kpzsproductions\Challengify\Models\User::find($challenge->getUserId());
                                        echo $creator ? e($creator->getUsername()) : 'Admin';
                                        ?>
                                    </span>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-users mt-1 mr-3 text-blue-500"></i>
                                <div>
                                    <span class="block text-gray-600">Submissions</span>
                                    <span class="font-medium"><?= count($submissions) ?></span>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-clock mt-1 mr-3 text-blue-500"></i>
                                <div>
                                    <span class="block text-gray-600">Time Commitment</span>
                                    <span class="font-medium">15-30 minutes</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- User's submission -->
        <?php if (isset($userSubmission)): ?>
            <div class="bg-white rounded-lg p-6 shadow-md mb-10">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold">Your Submission</h3>
                    <div>
                        <button id="edit-submission-btn" class="ch-btn ch-btn-outline-primary mr-2">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </button>
                        <a href="/challenges/<?= $challenge->getId() ?>/download-submission" class="ch-btn ch-btn-outline-success">
                            <i class="fas fa-download mr-1"></i> Download
                        </a>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h4 class="text-lg font-medium mb-1"><?= e($userSubmission->getTitle()) ?></h4>
                    <p class="text-gray-600 mb-3"><?= e($userSubmission->getDescription()) ?></p>
                    <div class="prose max-w-none"><?= nl2br(e($userSubmission->getContent())) ?></div>
                </div>
                
                <?php if ($userSubmission->getFilePath()): ?>
                    <div class="mt-5 border-t pt-4">
                        <h4 class="font-medium mb-2">Attached File</h4>
                        <?php 
                        $filePath = $userSubmission->getFilePath();
                        $fileName = basename($filePath);
                        $fileExt = pathinfo($filePath, PATHINFO_EXTENSION);
                        $isImage = in_array(strtolower($fileExt), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        $isVideo = in_array(strtolower($fileExt), ['mp4', 'webm', 'mov', 'avi']);
                        $isPdf = strtolower($fileExt) === 'pdf';
                        ?>
                        
                        <div class="mb-2">
                            <span class="text-sm font-medium"><?= e($fileName) ?></span>
                        </div>
                        
                        <div class="mt-2">
                            <?php if ($isImage): ?>
                                <div class="rounded-lg overflow-hidden border mb-2">
                                    <img src="<?= e($filePath) ?>" alt="Submission preview" class="max-h-96 mx-auto">
                                </div>
                            <?php elseif ($isVideo): ?>
                                <div class="rounded-lg overflow-hidden border mb-2">
                                    <video controls class="max-h-96 w-full">
                                        <source src="<?= e($filePath) ?>" type="video/<?= strtolower($fileExt) ?>">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                            <?php elseif ($isPdf): ?>
                                <div class="rounded-lg overflow-hidden border mb-2 h-96">
                                    <embed src="<?= e($filePath) ?>" type="application/pdf" class="w-full h-full">
                                </div>
                            <?php else: ?>
                                <div class="flex items-center px-4 py-3 bg-gray-50 rounded-lg">
                                    <i class="far fa-file-alt text-gray-500 text-2xl mr-3"></i>
                                    <span>File attached: <a href="<?= e($filePath) ?>" class="text-blue-600 hover:underline" target="_blank"><?= e($fileName) ?></a></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mt-2 text-sm text-gray-500">
                            Uploaded: <?= $userSubmission->getCreatedAt()->format('F j, Y \a\t g:i a') ?>
                            <?php if ($userSubmission->getUpdatedAt()): ?>
                                <span class="ml-2">(Updated: <?= $userSubmission->getUpdatedAt()->format('F j, Y \a\t g:i a') ?>)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="mt-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                        <?php if ($userSubmission->getStatus() === 'approved'): ?>
                            bg-green-100 text-green-800
                        <?php elseif ($userSubmission->getStatus() === 'rejected'): ?>
                            bg-red-100 text-red-800
                        <?php elseif ($userSubmission->getStatus() === 'draft'): ?>
                            bg-gray-100 text-gray-800
                        <?php else: ?>
                            bg-blue-100 text-blue-800
                        <?php endif; ?>
                    ">
                        Status: <?= ucfirst($userSubmission->getStatus()) ?>
                    </span>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/../partials/footer.php'; ?>

    <!-- Edit Submission Modal -->
    <?php if (isset($userSubmission)): ?>
    <div id="edit-submission-modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-8 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <h2 class="text-2xl font-bold mb-6">Edit Your Submission</h2>
            
            <?php if (isset($errors) && !empty($errors)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <strong class="font-bold">Please fix the following errors:</strong>
                    <ul class="list-disc ml-5 mt-1">
                        <?php foreach ($errors as $error): ?>
                            <li><?= e($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form action="/challenges/<?= $challenge->getId() ?>/submit" method="POST" enctype="multipart/form-data">
                <!-- CSRF Protection -->
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                
                <div class="mb-4">
                    <label for="edit-title" class="block text-gray-700 font-medium mb-2">Title</label>
                    <input type="text" id="edit-title" name="title" class="form-input w-full rounded-md" value="<?= e($userSubmission->getTitle()) ?>" required>
                </div>
                
                <div class="mb-4">
                    <label for="edit-description" class="block text-gray-700 font-medium mb-2">Description</label>
                    <textarea id="edit-description" name="description" rows="3" class="form-textarea w-full rounded-md" required><?= e($userSubmission->getDescription()) ?></textarea>
                </div>
                
                <div class="mb-4">
                    <label for="edit-content" class="block text-gray-700 font-medium mb-2">Content</label>
                    <textarea id="edit-content" name="content" rows="5" class="form-textarea w-full rounded-md" required><?= e($userSubmission->getContent()) ?></textarea>
                </div>
                
                <div class="mb-6">
                    <label for="edit-submission-file" class="block text-gray-700 font-medium mb-2">Attachment</label>
                    <?php if ($userSubmission->getFilePath()): ?>
                        <div class="mb-2">
                            <span class="text-sm">Current file: <?= basename(e($userSubmission->getFilePath())) ?></span>
                            <?php 
                            $fileExt = pathinfo($userSubmission->getFilePath(), PATHINFO_EXTENSION);
                            $isImage = in_array(strtolower($fileExt), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            
                            if ($isImage): ?>
                                <div class="mt-2 mb-3">
                                    <img src="<?= e($userSubmission->getFilePath()) ?>" alt="Submission image" class="max-h-32 rounded">
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <input type="file" id="edit-submission-file" name="submission_file" class="form-input w-full">
                    <p class="text-sm text-gray-500 mt-1">Supported formats:</p>
                    <ul class="text-sm text-gray-500 list-disc ml-5 mt-1">
                        <li>Images: JPG, PNG, GIF, WebP</li>
                        <li>Documents: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, CSV, HTML</li>
                        <li>Videos: MP4, MPEG, MOV, AVI, WMV, WebM</li>
                    </ul>
                    <p class="text-sm text-gray-500 mt-1">Maximum file size: 15MB</p>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button" id="cancel-edit" class="ch-btn ch-btn-outline-secondary">Cancel</button>
                    <button type="submit" class="ch-btn ch-btn-primary">Update Submission</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Voting Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const voteButtons = document.querySelectorAll('.vote-btn');
            
            // Check if user has already voted on each submission
            voteButtons.forEach(button => {
                const submissionId = button.getAttribute('data-submission');
                
                // Check if user has already voted on this submission
                fetch(`/vote/status/${submissionId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.has_voted) {
                            button.classList.add('text-blue-600');
                            button.disabled = true;
                        }
                    })
                    .catch(error => console.error('Error checking vote status:', error));
            });
            
            voteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    <?php if (!isLoggedIn()): ?>
                        alert('You need to be logged in to vote.');
                        return;
                    <?php endif; ?>
                    
                    const submissionId = this.getAttribute('data-submission');
                    const voteType = this.getAttribute('data-vote');
                    const countElement = this.querySelector('.vote-count');
                    
                    // Send AJAX request to vote endpoint
                    fetch('/vote', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `submission_id=${submissionId}&vote_type=${voteType}&csrf_token=<?= csrf_token() ?>`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update vote count
                            countElement.textContent = data.vote_count;
                            
                            // Disable button and change color
                            this.classList.add('text-blue-600');
                            this.disabled = true;
                        } else {
                            alert(data.message || 'An error occurred while processing your vote.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while processing your vote.');
                    });
                });
            });
        });
    </script>

    <!-- Bottom of the file, before closing body tag -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Submission modal
            const submitBtn = document.getElementById('submit-btn');
            const submitModal = document.getElementById('submit-modal');
            const closeModalBtn = document.getElementById('close-modal');
            
            if (submitBtn && submitModal) {
                submitBtn.addEventListener('click', function() {
                    submitModal.classList.remove('hidden');
                });
            }
            
            if (closeModalBtn && submitModal) {
                closeModalBtn.addEventListener('click', function() {
                    submitModal.classList.add('hidden');
                });
            }
            
            // Edit submission modal
            const editSubmissionBtn = document.getElementById('edit-submission-btn');
            const editModal = document.getElementById('edit-submission-modal');
            const cancelEditBtn = document.getElementById('cancel-edit');
            
            if (editSubmissionBtn && editModal) {
                editSubmissionBtn.addEventListener('click', function() {
                    editModal.classList.remove('hidden');
                });
            }
            
            if (cancelEditBtn && editModal) {
                cancelEditBtn.addEventListener('click', function() {
                    editModal.classList.add('hidden');
                });
            }
            
            // Close modals when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === submitModal) {
                    submitModal.classList.add('hidden');
                }
                if (event.target === editModal) {
                    editModal.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html> 