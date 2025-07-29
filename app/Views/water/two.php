<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Water Consumption Tracker</title>
    <script src="{{APP_BASE_URL}}assets/tailwind.css"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
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
        
        .conversion-card {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        }
        
        .history-item {
            transition: all 0.3s ease;
        }
        
        .history-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .brand-logo {
            width: 24px;
            height: 24px;
            object-fit: contain;
            margin-right: 8px;
        }
        
        .goal-meter {
            height: 8px;
            border-radius: 4px;
            background-color: #e5e7eb;
            overflow: hidden;
        }
        
        .goal-progress {
            height: 100%;
            background-color: #3b82f6;
            transition: width 0.5s ease;
        }
        
        .overflow-indicator {
            position: absolute;
            top: 0;
            width: 100%;
            background-color: rgba(255, 0, 0, 0.3);
            border-radius: 20px 20px 0 0;
        }
        
        .confetti-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1000;
        }
        
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1001;
        }
        
        .modal-content {
            background-color: white;
            padding: 2rem;
            border-radius: 0.5rem;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center p-4">
    <div id="confetti-container" class="confetti-container"></div>
    
    <div class="bg-white rounded-xl shadow-lg p-8 max-w-4xl w-full mb-8">
        <h1 class="text-3xl font-bold text-center text-blue-500 mb-6">
            <i class="fas fa-tint mr-2"></i>Water Consumption Tracker
        </h1>
        
        <div class="grid md:grid-cols-2 gap-8">
            <!-- Left Column - Bottle Visualization -->
            <div>
                <div class="bottle-container mb-8">
                    <div id="water" class="water-fill">
                        <div class="water-wave"></div>
                    </div>
                    <div id="overflowIndicator" class="overflow-indicator" style="display: none;"></div>
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
                
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-blue-50 p-3 rounded-lg">
                        <div class="text-sm text-blue-700">Total Goal</div>
                        <div id="totalGoal" class="text-xl font-bold text-blue-800">0.00 L</div>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-lg">
                        <div class="text-sm text-blue-700">Progress</div>
                        <div id="progressPercent" class="text-xl font-bold text-blue-800">0%</div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>0%</span>
                        <span id="progressText">0.00 L / 0.00 L</span>
                        <span>100%</span>
                    </div>
                    <div class="goal-meter">
                        <div id="goalProgress" class="goal-progress" style="width: 0%"></div>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-blue-50 p-3 rounded-lg">
                        <div class="text-sm text-blue-700">Total Consumed</div>
                        <div id="totalLiters" class="text-xl font-bold text-blue-800">0.00 L</div>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-lg">
                        <div class="text-sm text-blue-700">Total Gallons</div>
                        <div id="totalGallons" class="text-xl font-bold text-blue-800">0.00 gal</div>
                    </div>
                </div>
                
                <div class="conversion-card p-4 rounded-lg mb-6">
                    <h3 class="font-bold text-blue-800 mb-2">
                        <i class="fas fa-info-circle mr-2"></i>Conversion Legend
                    </h3>
                    <div class="text-sm">
                        <div class="flex justify-between py-1 border-b border-blue-100">
                            <span>1 liter</span>
                            <span>= 0.264172 gallons</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-blue-100">
                            <span>1 gallon</span>
                            <span>= 3.78541 liters</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span>Standard bottle</span>
                            <span>= 1 liter</span>
                        </div>
                    </div>
                </div>
                
                <button id="setGoalBtn" class="w-full bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-lg transition flex items-center justify-center">
                    <i class="fas fa-bullseye mr-2"></i> Set Total Goal
                </button>
            </div>
            
            <!-- Right Column - Input and History -->
            <div>
                <div class="bg-blue-50 rounded-lg p-4 mb-6">
                    <h2 class="text-xl font-bold text-blue-800 mb-4">
                        <i class="fas fa-plus-circle mr-2"></i>Add Consumption
                    </h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="brand" class="block text-sm font-medium text-gray-700 mb-1">Water Brand</label>
                            <div class="relative">
                                <select id="brand" class="w-full px-3 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    <option value="Generic">Generic</option>
                                    <option value="Evian">Evian</option>
                                    <option value="Fiji">Fiji</option>
                                    <option value="Dasani">Dasani</option>
                                    <option value="Aquafina">Aquafina</option>
                                    <option value="Smartwater">Smartwater</option>
                                    <option value="Voss">Voss</option>
                                    <option value="Perrier">Perrier</option>
                                    <option value="San Pellegrino">San Pellegrino</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                            <input type="date" id="date" class="w-full px-3 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label for="bottleSize" class="block text-sm font-medium text-gray-700 mb-1">Bottle Size (liters)</label>
                            <input type="number" id="bottleSize" min="0.1" max="5" step="0.1" value="1" 
                                   class="w-full px-3 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                            <div class="flex items-center">
                                <button id="decrementBottle" class="px-4 py-2 bg-gray-200 rounded-l-lg hover:bg-gray-300">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" id="quantity" min="1" value="1" 
                                       class="w-full px-3 py-2 border-t border-b border-gray-300 text-center focus:ring-blue-500 focus:border-blue-500">
                                <button id="incrementBottle" class="px-4 py-2 bg-gray-200 rounded-r-lg hover:bg-gray-300">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        
                        <button id="addEntryBtn" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg transition flex items-center justify-center">
                            <i class="fas fa-plus mr-2"></i> Add Entry
                        </button>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-4">
                    <h2 class="text-xl font-bold text-blue-800 mb-4">
                        <i class="fas fa-history mr-2"></i>Consumption History
                    </h2>
                    
                    <div id="historyList" class="space-y-2 max-h-64 overflow-y-auto pr-2">
                        <div class="text-center text-gray-500 py-4">
                            No entries yet. Add your first water consumption!
                        </div>
                    </div>
                    
                    <div class="mt-4 flex justify-between items-center">
                        <div class="text-sm text-gray-600">
                            <span id="totalEntries">0</span> entries
                        </div>
                        <button id="clearHistoryBtn" class="text-red-500 hover:text-red-700 text-sm font-medium">
                            <i class="fas fa-trash mr-1"></i> Clear All
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Goal Setting Modal -->
    <div id="goalModal" class="modal-overlay hidden">
        <div class="modal-content">
            <h2 class="text-2xl font-bold text-blue-800 mb-4">
                <i class="fas fa-bullseye mr-2"></i>Set Your Water Goal
            </h2>
            <div class="mb-4">
                <label for="goalInput" class="block text-sm font-medium text-gray-700 mb-1">Total Water Goal (liters)</label>
                <input type="number" id="goalInput" min="1" step="0.1" class="w-full px-3 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. 10000">
            </div>
            <div class="flex justify-end space-x-2">
                <button id="cancelGoalBtn" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Cancel</button>
                <button id="saveGoalBtn" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Save Goal</button>
            </div>
        </div>
    </div>

    <script>
        // DOM Elements
        const water = document.getElementById('water');
        const overflowIndicator = document.getElementById('overflowIndicator');
        const bottleSize = document.getElementById('bottleSize');
        const quantity = document.getElementById('quantity');
        const totalLiters = document.getElementById('totalLiters');
        const totalGallons = document.getElementById('totalGallons');
        const progressPercent = document.getElementById('progressPercent');
        const progressText = document.getElementById('progressText');
        const totalGoal = document.getElementById('totalGoal');
        const goalProgress = document.getElementById('goalProgress');
        const decrementBottle = document.getElementById('decrementBottle');
        const incrementBottle = document.getElementById('incrementBottle');
        const addEntryBtn = document.getElementById('addEntryBtn');
        const clearHistoryBtn = document.getElementById('clearHistoryBtn');
        const historyList = document.getElementById('historyList');
        const totalEntries = document.getElementById('totalEntries');
        const brand = document.getElementById('brand');
        const dateInput = document.getElementById('date');
        const setGoalBtn = document.getElementById('setGoalBtn');
        const goalModal = document.getElementById('goalModal');
        const goalInput = document.getElementById('goalInput');
        const saveGoalBtn = document.getElementById('saveGoalBtn');
        const cancelGoalBtn = document.getElementById('cancelGoalBtn');
        const confettiContainer = document.getElementById('confetti-container');
        
        // Initialize with today's date
        dateInput.valueAsDate = new Date();
        
        // Water consumption data
        let consumptionHistory = JSON.parse(localStorage.getItem('waterConsumption')) || [];
        let totalLitersConsumed = 0;
        let totalGallonsConsumed = 0;
        let currentTotalGoal = parseFloat(localStorage.getItem('totalWaterGoal')) || 0;
        let goalReached = false;
        
        // Brand logos (using emoji as fallback)
        const brandLogos = {
            'Generic': '💧',
            'Evian': '🏔️',
            'Fiji': '🌴',
            'Dasani': '🏢',
            'Aquafina': '🏭',
            'Smartwater': '🧠',
            'Voss': '❄️',
            'Perrier': '🍾',
            'San Pellegrino': '🇮🇹',
            'Other': '🥤'
        };
        
        // Initialize
        updateTotalGoalDisplay();
        updateTotals();
        renderHistory();
        
        // Update water level visualization based on consumed liters
        function updateWaterLevel(liters) {
            if (currentTotalGoal <= 0) {
                water.style.height = '0%';
                return;
            }
            
            // Calculate percentage of total goal
            let percentage = (liters / currentTotalGoal) * 100;
            percentage = Math.max(0, Math.min(100, percentage));
            
            // Calculate height (92% of bottle height is usable space)
            const height = (percentage / 100) * 92;
            water.style.height = `${height}%`;
            
            // Handle overflow (if more than total goal)
            if (liters > currentTotalGoal) {
                overflowIndicator.style.display = 'block';
                const overflowPercentage = ((liters - currentTotalGoal) / currentTotalGoal) * 10; // Show max 10% overflow
                overflowIndicator.style.height = `${Math.min(10, overflowPercentage)}%`;
                water.style.height = '92%'; // Main bottle remains full
            } else {
                overflowIndicator.style.display = 'none';
            }
            
            // Change color based on percentage of total goal
            let hue;
            if (percentage < 25) {
                hue = 210; // blue
            } else if (percentage < 50) {
                hue = 180; // aqua
            } else if (percentage < 75) {
                hue = 150; // teal
            } else if (percentage < 100) {
                hue = 120; // green
            } else {
                hue = 100; // bright green for goal reached
            }
            
            water.style.backgroundColor = `hsl(${hue}, 90%, 55%)`;
            
            // Check if goal was just reached
            if (!goalReached && percentage >= 100 && currentTotalGoal > 0) {
                goalReached = true;
                celebrateGoal();
            } else if (percentage < 100) {
                goalReached = false;
            }
        }
        
        // Update total goal display
        function updateTotalGoalDisplay() {
            totalGoal.textContent = currentTotalGoal > 0 ? `${currentTotalGoal.toFixed(2)} L` : 'Not set';
        }
        
        // Calculate and update totals
        function updateTotals() {
            totalLitersConsumed = consumptionHistory.reduce((sum, entry) => sum + (entry.size * entry.quantity), 0);
            totalGallonsConsumed = totalLitersConsumed * 0.264172;
            
            totalLiters.textContent = totalLitersConsumed.toFixed(2) + ' L';
            totalGallons.textContent = totalGallonsConsumed.toFixed(2) + ' gal';
            
            // Calculate progress percentage against total goal
            const progressPercentage = currentTotalGoal > 0 ? (totalLitersConsumed / currentTotalGoal) * 100 : 0;
            progressPercent.textContent = `${Math.min(100, progressPercentage).toFixed(0)}%`;
            progressText.textContent = currentTotalGoal > 0 
                ? `${totalLitersConsumed.toFixed(2)} L / ${currentTotalGoal.toFixed(2)} L`
                : `${totalLitersConsumed.toFixed(2)} L (no goal set)`;
            
            // Update progress bar
            goalProgress.style.width = `${Math.min(100, progressPercentage)}%`;
            
            // Update bottle visualization based on actual consumed liters
            updateWaterLevel(totalLitersConsumed);
            
            // Update entries count
            totalEntries.textContent = consumptionHistory.length;
        }
        
        // Render consumption history
        function renderHistory() {
            if (consumptionHistory.length === 0) {
                historyList.innerHTML = `
                    <div class="text-center text-gray-500 py-4">
                        No entries yet. Add your first water consumption!
                    </div>
                `;
                return;
            }
            
            // Sort by date (newest first)
            const sortedHistory = [...consumptionHistory].sort((a, b) => new Date(b.date) - new Date(a.date));
            
            historyList.innerHTML = sortedHistory.map(entry => `
                <div class="history-item bg-blue-50 rounded-lg p-3 flex justify-between items-center">
                    <div class="flex items-center">
                        <span class="brand-logo text-lg">${brandLogos[entry.brand] || brandLogos.Generic}</span>
                        <div>
                            <div class="font-medium">${entry.brand}</div>
                            <div class="text-xs text-gray-600">${new Date(entry.date).toLocaleDateString()}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold">${(entry.size * entry.quantity).toFixed(2)} L</div>
                        <div class="text-xs text-gray-600">${entry.quantity} × ${entry.size}L</div>
                    </div>
                </div>
            `).join('');
        }
        
        // Save to localStorage
        function saveData() {
            localStorage.setItem('waterConsumption', JSON.stringify(consumptionHistory));
            localStorage.setItem('totalWaterGoal', currentTotalGoal.toString());
        }
        
        // Celebrate goal achievement with confetti
        function celebrateGoal() {
            // Create confetti
            confetti({
                particleCount: 150,
                spread: 70,
                origin: { y: 0.6 },
                colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']
            });
            
            // Show congratulatory message
            setTimeout(() => {
                alert('🎉 Congratulations! You have reached your water consumption goal! 🎉');
            }, 500);
        }
        
        // Event Listeners
        decrementBottle.addEventListener('click', () => {
            if (quantity.value > 1) {
                quantity.value--;
            }
        });
        
        incrementBottle.addEventListener('click', () => {
            quantity.value++;
        });
        
        addEntryBtn.addEventListener('click', () => {
            const entry = {
                brand: brand.value,
                date: dateInput.value,
                size: parseFloat(bottleSize.value),
                quantity: parseInt(quantity.value),
                timestamp: new Date().getTime()
            };
            
            consumptionHistory.push(entry);
            saveData();
            updateTotals();
            renderHistory();
            
            // Show success feedback
            const originalText = addEntryBtn.innerHTML;
            addEntryBtn.innerHTML = `<i class="fas fa-check mr-2"></i> Added!`;
            addEntryBtn.classList.remove('bg-blue-500', 'hover:bg-blue-600');
            addEntryBtn.classList.add('bg-green-500', 'hover:bg-green-600');
            
            setTimeout(() => {
                addEntryBtn.innerHTML = originalText;
                addEntryBtn.classList.remove('bg-green-500', 'hover:bg-green-600');
                addEntryBtn.classList.add('bg-blue-500', 'hover:bg-blue-600');
            }, 2000);
        });
        
        clearHistoryBtn.addEventListener('click', () => {
            if (confirm('Are you sure you want to clear all consumption history?')) {
                consumptionHistory = [];
                saveData();
                updateTotals();
                renderHistory();
            }
        });
        
        // Handle quantity input validation
        quantity.addEventListener('change', () => {
            if (quantity.value < 1) quantity.value = 1;
        });
        
        // Goal setting modal
        setGoalBtn.addEventListener('click', () => {
            goalInput.value = currentTotalGoal > 0 ? currentTotalGoal : '';
            goalModal.classList.remove('hidden');
        });
        
        saveGoalBtn.addEventListener('click', () => {
            const newGoal = parseFloat(goalInput.value);
            if (!isNaN(newGoal) && newGoal > 0) {
                currentTotalGoal = newGoal;
                updateTotalGoalDisplay();
                updateTotals();
                saveData();
                goalModal.classList.add('hidden');
            } else {
                alert('Please enter a valid positive number');
            }
        });
        
        cancelGoalBtn.addEventListener('click', () => {
            goalModal.classList.add('hidden');
        });
    </script>
</body>
</html>