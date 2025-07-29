<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Water Bottle Filler</title>
    <script src="{{APP_BASE_URL}}assets/tailwind.css"></script>
    <style>
        .bottle-container {
            position: relative;
            width: 150px;
            height: 300px;
            margin: 0 auto;
        }
        
        .water-fill {
            position: absolute;
            bottom: 0;
            width: 100%;
            background-color: #60a5fa;
            transition: height 0.5s ease;
            border-radius: 0 0 20px 20px;
        }
        
        .water-wave {
            position: absolute;
            top: -10px;
            width: 100%;
            height: 20px;
            background: url('data:image/svg+xml;utf8,<svg viewBox="0 0 120 20" xmlns="http://www.w3.org/2000/svg"><path fill="%2360a5fa" d="M0,10 C20,20 40,0 60,10 C80,20 100,0 120,10 L120,20 L0,20 Z"/></svg>');
            background-size: 120px 20px;
            animation: wave 3s linear infinite;
        }
        
        @keyframes wave {
            0% { background-position-x: 0; }
            100% { background-position-x: 120px; }
        }
        
        .bottle {
            position: relative;
            z-index: 2;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-lg p-8 max-w-md w-full">
        <h1 class="text-3xl font-bold text-center text-blue-500 mb-6">Water Bottle Filler</h1>
        
        <div class="bottle-container mb-8">
            <div id="water" class="water-fill">
                <div class="water-wave"></div>
            </div>
            <svg class="bottle" width="150" height="300" viewBox="0 0 150 300" xmlns="http://www.w3.org/2000/svg">
                <!-- Bottle outline -->
                <path d="M75 20 C90 20 100 30 100 50 L100 280 C100 290 90 300 75 300 C60 300 50 290 50 280 L50 50 C50 30 60 20 75 20 Z" 
                      fill="none" stroke="#4b5563" stroke-width="3"/>
                
                <!-- Bottle cap -->
                <rect x="60" y="10" width="30" height="10" rx="2" fill="#9ca3af" stroke="#4b5563" stroke-width="1"/>
                
                <!-- Water level indicator marks -->
                <line x1="40" y1="250" x2="50" y2="250" stroke="#4b5563" stroke-width="2"/>
                <text x="35" y="253" font-size="10" fill="#4b5563">0%</text>
                
                <line x1="40" y1="200" x2="50" y2="200" stroke="#4b5563" stroke-width="2"/>
                <text x="35" y="203" font-size="10" fill="#4b5563">25%</text>
                
                <line x1="40" y1="150" x2="50" y2="150" stroke="#4b5563" stroke-width="2"/>
                <text x="35" y="153" font-size="10" fill="#4b5563">50%</text>
                
                <line x1="40" y1="100" x2="50" y2="100" stroke="#4b5563" stroke-width="2"/>
                <text x="35" y="103" font-size="10" fill="#4b5563">75%</text>
                
                <line x1="40" y1="50" x2="50" y2="50" stroke="#4b5563" stroke-width="2"/>
                <text x="35" y="53" font-size="10" fill="#4b5563">100%</text>
            </svg>
        </div>
        
        <div class="flex flex-col space-y-4">
            <div class="flex items-center justify-between">
                <span class="font-medium">Fill Level:</span>
                <span id="percentage" class="font-bold text-blue-600">0%</span>
            </div>
            
            <input type="range" id="fillSlider" min="0" max="100" value="0" 
                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
            
            <div class="flex space-x-4 mt-4">
                <button id="fillBtn" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg transition">
                    Fill Bottle
                </button>
                <button id="emptyBtn" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg transition">
                    Empty Bottle
                </button>
            </div>
            
            <div class="flex space-x-4">
                <button id="add10Btn" class="flex-1 bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-lg transition">
                    +10%
                </button>
                <button id="remove10Btn" class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-lg transition">
                    -10%
                </button>
            </div>
        </div>
    </div>

    <script>
        const water = document.getElementById('water');
        const percentageDisplay = document.getElementById('percentage');
        const fillSlider = document.getElementById('fillSlider');
        const fillBtn = document.getElementById('fillBtn');
        const emptyBtn = document.getElementById('emptyBtn');
        const add10Btn = document.getElementById('add10Btn');
        const remove10Btn = document.getElementById('remove10Btn');
        
        let currentLevel = 0;
        
        function updateWaterLevel(level) {
            // Ensure level is between 0 and 100
            level = Math.max(0, Math.min(100, level));
            currentLevel = level;
            
            // Calculate height (from 0% to 92% of container to leave space for cap)
            const height = (level / 100) * 92;
            
            // Update water element
            water.style.height = `${height}%`;
            
            // Update percentage display
            percentageDisplay.textContent = `${level}%`;
            
            // Update slider
            fillSlider.value = level;
            
            // Change water color based on level
            const hue = 210 - (level * 0.5); // Blue gets lighter as bottle fills
            water.style.backgroundColor = `hsl(${hue}, 90%, 70%)`;
        }
        
        // Slider input
        fillSlider.addEventListener('input', () => {
            updateWaterLevel(parseInt(fillSlider.value));
        });
        
        // Fill button
        fillBtn.addEventListener('click', () => {
            updateWaterLevel(100);
        });
        
        // Empty button
        emptyBtn.addEventListener('click', () => {
            updateWaterLevel(0);
        });
        
        // Add 10% button
        add10Btn.addEventListener('click', () => {
            updateWaterLevel(currentLevel + 10);
        });
        
        // Remove 10% button
        remove10Btn.addEventListener('click', () => {
            updateWaterLevel(currentLevel - 10);
        });
        
        // Initialize
        updateWaterLevel(0);
    </script>
</body>
</html>