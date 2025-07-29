<?php defined('COREPATH') or exit('No direct script access allowed'); ?></html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Webby PHP Framework</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="<?= APP_BASE_URL . 'assets/tailwind.css?plugins=typography' ?>"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        webby: "#6d00cc",
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer components {
            .bg-primary {
                @apply bg-gradient-to-br
                from-purple-100 
                to-pink-100 
                via-cyan-100;
            }
        }
    </style>
    <style>
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        .floating {
            animation: float 6s ease-in-out infinite;
        }
        .gradient-text {
            background: linear-gradient(90deg, #3b82f6, #8b5cf6, #ec4899);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .glow {
            text-shadow: 0 0 10px rgba(59, 130, 246, 0.7);
        }
        .terminal-code {
            background-color: #1e293b;
            border-radius: 0.5rem;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>

<body class="bg-gray-900 text-white min-h-screen flex flex-col items-center justify-center p-4">
    <div class="max-w-4xl w-full text-center">
        <!-- Floating Logo -->
        <div class="floating mb-8">
            <!-- <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-32 h-32 mx-auto text-blue-400">
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
            </svg> -->

            <img class="w-[40%] mx-auto" src="<?=asset('webby-readme.png')?>" alt="">
        </div>

        <!-- Main Heading -->
        <h1 class="text-5xl md:text-6xl font-bold mb-6 gradient-text glow">
            Welcome to <span class="text-blue-400">Webby</span>
        </h1>

        <!-- Subheading -->
        <p class="text-xl md:text-2xl text-gray-300 mb-8">
            A "lego-like" PHP framework for building Simple Applications 
        </p>

        <!-- Terminal-like Code Block -->
        <div class="terminal-code p-6 mb-10 text-left max-w-2xl mx-auto">
            <div class="flex mb-4">
                <div class="w-3 h-3 rounded-full bg-red-500 mr-2"></div>
                <div class="w-3 h-3 rounded-full bg-yellow-500 mr-2"></div>
                <div class="w-3 h-3 rounded-full bg-green-500"></div>
            </div>
            <!-- <pre class="text-green-400 overflow-x-auto">
                <span class="text-gray-400">$</span> <span class="text-blue-300">php webby serve</span>
                <span class="text-gray-400">Webby development server started:</span>
                <span class="text-gray-400">Listening on http://localhost:8000</span>
                <span class="text-gray-400">Press Ctrl+C to stop the server</span>
            </pre> -->
<pre class="text-green-400 overflow-x-auto">
$ php webby serve
  PHP Built-In Web Server Started for Webby
  Navigate to <?= $baseURL ?? 'http://localhost:8085' ?> to view your project.
  Press Ctrl+C to stop the server
</pre>
            
        </div>

        <!-- Quick Start Buttons -->
        <div class="flex flex-wrap justify-center gap-4 mb-12">
            <a href="{{url('docs')}}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 rounded-lg font-medium transition-all transform hover:scale-105">
                <i class="fas fa-book mr-2"></i> Documentation
            </a>
            <a href="{{url('save/download')}}" class="px-6 py-3 bg-purple-600 hover:bg-purple-700 rounded-lg font-medium transition-all transform hover:scale-105">
                <i class="fas fa-download mr-2"></i> Download
            </a>
            <a href="{{url('repo')}}" class="px-6 py-3 bg-pink-600 hover:bg-pink-700 rounded-lg font-medium transition-all transform hover:scale-105">
                <i class="fab fa-github mr-2"></i> GitHub
            </a>
        </div>

        <!-- Features Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-gray-800 p-6 rounded-xl hover:bg-gray-700 transition-all">
                <div class="text-blue-400 text-3xl mb-4">
                    <i class="fas fa-bolt"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Lightning Fast</h3>
                <p class="text-gray-400">Optimized for performance with minimal overhead.</p>
            </div>
            <div class="bg-gray-800 p-6 rounded-xl hover:bg-gray-700 transition-all">
                <div class="text-purple-400 text-3xl mb-4">
                    <i class="fas fa-cubes"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Modular</h3>
                <p class="text-gray-400">Use only what you need, leave the rest behind.</p>
            </div>
            <div class="bg-gray-800 p-6 rounded-xl hover:bg-gray-700 transition-all">
                <div class="text-pink-400 text-3xl mb-4">
                    <i class="fas fa-heart"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Developer Friendly</h3>
                <p class="text-gray-400">Elegant syntax and intuitive API design.</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-gray-500 text-sm">
            <p>Webby PHP Framework <?php echo (ENVIRONMENT === 'development') ?  ' <strong>v' . WEBBY_VERSION . ' (PHP v' . phpversion() . ')</strong>' : '' ?></p>
            <p>Rendered in &nbsp; <strong> {elapsed_time} </strong> &nbsp; seconds | &nbsp; With <strong>{memory_usage}</strong> &nbsp; memory used.</p>
            <p class="mt-2">© 2025 Webby Team. All rights reserved.</p>
        </div>
    </div>

    <!-- Animated Background Elements -->
    <div class="fixed inset-0 overflow-hidden -z-10">
        <div class="absolute top-1/4 left-1/4 w-16 h-16 rounded-full bg-blue-900 opacity-20 blur-3xl"></div>
        <div class="absolute top-1/3 right-1/4 w-24 h-24 rounded-full bg-purple-900 opacity-20 blur-3xl"></div>
        <div class="absolute bottom-1/4 right-1/3 w-20 h-20 rounded-full bg-pink-900 opacity-20 blur-3xl"></div>
    </div>

    <script>
        // Add some interactive elements
        document.addEventListener('DOMContentLoaded', () => {
            const logo = document.querySelector('.floating');
            
            logo.addEventListener('mouseenter', () => {
                logo.style.animation = 'float 3s ease-in-out infinite';
            });
            
            logo.addEventListener('mouseleave', () => {
                logo.style.animation = 'float 6s ease-in-out infinite';
            });
            
            // Typewriter effect for terminal code
            const terminalText = document.querySelector('.terminal-code pre');
            const originalText = terminalText.innerHTML;
            terminalText.innerHTML = '';
            
            let i = 0;
            const typeWriter = () => {
                if (i < originalText.length) {
                    terminalText.innerHTML += originalText.charAt(i);
                    i++;
                    setTimeout(typeWriter, Math.random() * 50 + 20);
                }
            };
            
            setTimeout(typeWriter, 1000);
        });
    </script>
</body>
</html>