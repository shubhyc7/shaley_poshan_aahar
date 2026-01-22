<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shaley Poshan Aahar System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar-brand {
            font-weight: bold;
            letter-spacing: 1px;
        }

        .main-container {
            margin-top: 30px;
            margin-bottom: 50px;
        }

        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url() ?>">
                <i class="fas fa-utensils me-2"></i> POSHAN AAHAR
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('entries') ?>">
                            <i class="fas fa-edit me-1"></i> दैनंदिन नोंद
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('StudentStrength') ?>">
                            <i class="fas fa-users me-1"></i> विद्यार्थी संख्या
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('items') ?>">
                            <i class="fas fa-list me-1"></i> वस्तू मास्टर
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('ItemRates') ?>">
                            <i class="fas fa-list me-1"></i> वस्तू दर
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('stock') ?>">
                            <i class="fas fa-box me-1"></i> स्टॉक
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-warning fw-bold" href="<?= base_url('reports') ?>">
                            <i class="fas fa-file-alt me-1"></i> मासिक अहवाल
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container main-container">
        <div class="row">
            <div class="col-md-12">
                <?= $this->renderSection('content') ?>
            </div>
        </div>
    </div>

    <footer class="text-center py-4 bg-white border-top mt-auto">
        <p class="text-muted mb-0">&copy; <?= date('Y') ?> शालेय पोषण आहार व्यवस्थापन प्रणाली</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Google Input Tools API for English to Marathi Transliteration -->
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
        // Load Google Input Tools correctly - using the proper API
        google.load("inputtools", "1");
        
        var globalControl = null;
        var transliterationInitialized = false;

        function initializeTransliteration() {
            if (transliterationInitialized) return;
            
            try {
                var options = {
                    sourceLanguage: google.elements.transliteration.LanguageCode.ENGLISH,
                    destinationLanguage: [google.elements.transliteration.LanguageCode.MARATHI],
                    shortcutKey: 'ctrl+g',
                    transliterationEnabled: true
                };

                if (!globalControl) {
                    globalControl = new google.elements.transliteration.TransliterationControl(options);
                }

                // Get all text input fields (excluding those with data-no-translit attribute)
                var textInputs = document.querySelectorAll('input[type="text"]:not([data-no-translit]):not([data-translit-init])');
                
                if (textInputs.length > 0) {
                    var inputArray = Array.from(textInputs);
                    globalControl.makeTransliteratable(inputArray);
                    
                    // Mark inputs as initialized and add space key handler
                    inputArray.forEach(function(input) {
                        input.setAttribute('data-translit-init', 'true');
                        
                        // Enable transliteration by default for item name fields
                        if (input.name === 'item_name' || input.id === 'edit_item_name') {
                            try {
                                if (globalControl) {
                                    globalControl.enableTransliteration([input]);
                                }
                            } catch(e) {
                                console.log('Error enabling transliteration:', e);
                            }
                        }
                        
                        // Handle space key to convert last word
                        input.addEventListener('keydown', function(e) {
                            if (e.key === ' ' || e.keyCode === 32) {
                                var inputEl = this;
                                var cursorPos = inputEl.selectionStart;
                                var textBeforeCursor = inputEl.value.substring(0, cursorPos);
                                var words = textBeforeCursor.trim().split(/\s+/);
                                var lastWord = words.length > 0 ? words[words.length - 1] : '';
                                
                                // If last word contains only English letters, ensure conversion happens
                                if (lastWord && /^[a-zA-Z]+$/.test(lastWord) && lastWord.length > 0) {
                                    // Prevent default space insertion temporarily
                                    e.preventDefault();
                                    
                                    // Get the text after cursor
                                    var textAfterCursor = inputEl.value.substring(cursorPos);
                                    
                                    // Insert space first
                                    var newValue = textBeforeCursor + ' ' + textAfterCursor;
                                    inputEl.value = newValue;
                                    inputEl.setSelectionRange(cursorPos + 1, cursorPos + 1);
                                    
                                    // Small delay to let Google transliteration process the word before space
                                    setTimeout(function() {
                                        // Check if conversion happened
                                        var currentText = inputEl.value.substring(0, inputEl.selectionStart);
                                        var currentWords = currentText.trim().split(/\s+/);
                                        var wordBeforeSpace = currentWords.length > 1 ? currentWords[currentWords.length - 2] : '';
                                        
                                        // If word is still English, try manual conversion
                                        if (wordBeforeSpace && /^[a-zA-Z]+$/.test(wordBeforeSpace)) {
                                            // Trigger transliteration manually
                                            var event = new Event('input', { bubbles: true });
                                            inputEl.dispatchEvent(event);
                                        }
                                    }, 150);
                                }
                            }
                        });
                    });
                }
                
                transliterationInitialized = true;
            } catch (e) {
                console.log('Transliteration initialization error:', e);
            }
        }

        // Initialize when Google API is loaded
        google.setOnLoadCallback(function() {
            // Wait a bit for the API to be fully ready
            setTimeout(function() {
                initializeTransliteration();
            }, 500);
        });

        // Also initialize on page load with delay
        $(document).ready(function() {
            setTimeout(function() {
                if (typeof google !== 'undefined' && google.elements && google.elements.transliteration) {
                    initializeTransliteration();
                }
            }, 2000);
        });

        // Re-initialize when modals are shown (for dynamically added inputs)
        document.addEventListener('shown.bs.modal', function(event) {
            setTimeout(function() {
                initializeTransliteration();
            }, 500);
        });

        // Also initialize on DOM changes (for dynamically added content)
        if (typeof MutationObserver !== 'undefined') {
            var observer = new MutationObserver(function(mutations) {
                var hasNewInputs = false;
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes.length > 0) {
                        mutation.addedNodes.forEach(function(node) {
                            if (node.nodeType === 1) {
                                if (node.tagName === 'INPUT' && node.type === 'text' && !node.hasAttribute('data-translit-init')) {
                                    hasNewInputs = true;
                                } else if (node.querySelectorAll) {
                                    var inputs = node.querySelectorAll('input[type="text"]:not([data-translit-init])');
                                    if (inputs.length > 0) {
                                        hasNewInputs = true;
                                    }
                                }
                            }
                        });
                    }
                });
                
                if (hasNewInputs) {
                    setTimeout(function() {
                        initializeTransliteration();
                    }, 300);
                }
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
    </script>
    

    <style>
        /* Style for transliteration enabled indicator */
        input[type="text"][data-translit-init] {
            position: relative;
        }
        
        /* Add a small indicator when transliteration is active */
        .goog-te-menu-value {
            font-size: 12px;
        }
        
        /* Helper text for transliteration */
        .translit-helper {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 2px;
        }
        
        /* Style for text inputs to show transliteration is available */
        .form-control[data-translit-init]::after {
            content: " (Press Ctrl+G for Marathi)";
            font-size: 0.7rem;
            color: #6c757d;
        }
    </style>
    
    <script>
        // Add helper text to text inputs after transliteration is initialized
        $(document).ready(function() {
            setTimeout(function() {
                $('input[type="text"][data-translit-init]').each(function() {
                    var $input = $(this);
                    if ($input.next('.translit-helper').length === 0) {
                        $input.after('<small class="translit-helper d-block">मराठी टाइपिंगसाठी Ctrl+G दाबा</small>');
                    }
                });
            }, 2000);
            
            // Also add helper when new inputs are added
            var observer = new MutationObserver(function() {
                setTimeout(function() {
                    $('input[type="text"][data-translit-init]').each(function() {
                        var $input = $(this);
                        if ($input.next('.translit-helper').length === 0) {
                            $input.after('<small class="translit-helper d-block">मराठी टाइपिंगसाठी Ctrl+G दाबा</small>');
                        }
                    });
                }, 500);
            });
            
            if (typeof MutationObserver !== 'undefined') {
                observer.observe(document.body, {
                    childList: true,
                    subtree: true
                });
            }
        });
    </script>

</body>

</html>