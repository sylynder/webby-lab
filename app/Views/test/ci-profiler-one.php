<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeIgniter 3 Log Viewer & Profiler</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        .log-error { @apply bg-red-50 border-red-200 text-red-800; }
        .log-warning { @apply bg-yellow-50 border-yellow-200 text-yellow-800; }
        .log-info { @apply bg-blue-50 border-blue-200 text-blue-800; }
        .log-debug { @apply bg-gray-50 border-gray-200 text-gray-800; }
        .profiler-section { @apply bg-gradient-to-r from-purple-600 to-blue-600 text-white; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen" x-data="logProfiler()">
    <!-- Header -->
    <header class="bg-white shadow-lg border-b border-gray-200">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-fire text-orange-500 text-2xl"></i>
                    <h1 class="text-2xl font-bold text-gray-800">CodeIgniter 3 Log Profiler</h1>
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">PHP 8.3</span>
                </div>
                <div class="flex items-center space-x-4">
                    <button @click="refreshLogs()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-refresh mr-2"></i>Refresh
                    </button>
                    <button @click="clearLogs()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-trash mr-2"></i>Clear Logs
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8">
        <!-- Tab Navigation -->
        <div class="flex space-x-1 bg-white p-1 rounded-lg shadow mb-6">
            <button @click="activeTab = 'logs'" 
                    :class="activeTab === 'logs' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-gray-800'"
                    class="px-6 py-3 rounded-md font-medium transition-colors">
                <i class="fas fa-file-text mr-2"></i>Application Logs
            </button>
            <button @click="activeTab = 'profiler'" 
                    :class="activeTab === 'profiler' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-gray-800'"
                    class="px-6 py-3 rounded-md font-medium transition-colors">
                <i class="fas fa-tachometer-alt mr-2"></i>Performance Profiler
            </button>
            <button @click="activeTab = 'queries'" 
                    :class="activeTab === 'queries' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-gray-800'"
                    class="px-6 py-3 rounded-md font-medium transition-colors">
                <i class="fas fa-database mr-2"></i>Database Queries
            </button>
        </div>

        <!-- Logs Tab -->
        <div x-show="activeTab === 'logs'" x-cloak class="space-y-6">
            <!-- Filters -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Log Filters</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Log Level</label>
                        <select x-model="logFilter.level" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Levels</option>
                            <option value="ERROR">Error</option>
                            <option value="WARNING">Warning</option>
                            <option value="INFO">Info</option>
                            <option value="DEBUG">Debug</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                        <input x-model="logFilter.date" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input x-model="logFilter.search" type="text" placeholder="Search logs..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex items-end">
                        <button @click="applyFilters()" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                            <i class="fas fa-filter mr-2"></i>Apply Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Log Entries -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-800">Log Entries</h3>
                    <p class="text-sm text-gray-600 mt-1">Showing <span x-text="filteredLogs.length"></span> entries</p>
                </div>
                <div class="max-h-96 overflow-y-auto">
                    <template x-for="log in filteredLogs" :key="log.id">
                        <div class="border-b border-gray-100 p-4 hover:bg-gray-50 transition-colors"
                             :class="getLogClass(log.level)">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium"
                                              :class="getLevelClass(log.level)"
                                              x-text="log.level"></span>
                                        <span class="text-sm text-gray-500" x-text="log.timestamp"></span>
                                        <span class="text-sm text-gray-500" x-text="log.file"></span>
                                    </div>
                                    <p class="text-gray-800 font-medium mb-1" x-text="log.message"></p>
                                    <div x-show="log.context" class="text-sm text-gray-600 bg-gray-100 p-2 rounded mt-2">
                                        <pre x-text="log.context"></pre>
                                    </div>
                                </div>
                                <button @click="toggleLogDetails(log.id)" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Profiler Tab -->
        <div x-show="activeTab === 'profiler'" x-cloak class="space-y-6">
            <!-- Performance Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-lg shadow">
                    <div class="flex items-center">
                        <i class="fas fa-clock text-2xl mr-3"></i>
                        <div>
                            <p class="text-blue-100">Total Execution Time</p>
                            <p class="text-2xl font-bold" x-text="profilerData.executionTime + 'ms'"></p>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-lg shadow">
                    <div class="flex items-center">
                        <i class="fas fa-memory text-2xl mr-3"></i>
                        <div>
                            <p class="text-green-100">Memory Usage</p>
                            <p class="text-2xl font-bold" x-text="profilerData.memoryUsage + 'MB'"></p>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-6 rounded-lg shadow">
                    <div class="flex items-center">
                        <i class="fas fa-database text-2xl mr-3"></i>
                        <div>
                            <p class="text-purple-100">Database Queries</p>
                            <p class="text-2xl font-bold" x-text="profilerData.queryCount"></p>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white p-6 rounded-lg shadow">
                    <div class="flex items-center">
                        <i class="fas fa-file text-2xl mr-3"></i>
                        <div>
                            <p class="text-orange-100">Files Loaded</p>
                            <p class="text-2xl font-bold" x-text="profilerData.filesLoaded"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Profiler Sections -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Benchmarks -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="profiler-section px-6 py-4">
                        <h3 class="text-lg font-semibold">Benchmarks</h3>
                    </div>
                    <div class="p-6">
                        <template x-for="benchmark in profilerData.benchmarks" :key="benchmark.name">
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="font-medium text-gray-700" x-text="benchmark.name"></span>
                                <span class="text-sm text-gray-500" x-text="benchmark.time + 'ms'"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Memory Usage -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="profiler-section px-6 py-4">
                        <h3 class="text-lg font-semibold">Memory Usage</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Peak Memory</span>
                                <span class="font-semibold" x-text="profilerData.peakMemory + 'MB'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Current Memory</span>
                                <span class="font-semibold" x-text="profilerData.currentMemory + 'MB'"></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" :style="`width: ${(profilerData.currentMemory / profilerData.peakMemory) * 100}%`"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Queries Tab -->
        <div x-show="activeTab === 'queries'" x-cloak class="space-y-6">
            <!-- Query Statistics -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Query Statistics</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600" x-text="queryStats.total"></div>
                        <div class="text-sm text-gray-600">Total Queries</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600" x-text="queryStats.avgTime + 'ms'"></div>
                        <div class="text-sm text-gray-600">Average Time</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-red-600" x-text="queryStats.slowest + 'ms'"></div>
                        <div class="text-sm text-gray-600">Slowest Query</div>
                    </div>
                </div>
            </div>

            <!-- Query List -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-800">Database Queries</h3>
                </div>
                <div class="divide-y divide-gray-200">
                    <template x-for="(query, index) in queries" :key="index">
                        <div class="p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex justify-between items-start mb-3">
                                <span class="px-2 py-1 text-xs font-medium rounded-full"
                                      :class="query.time > 100 ? 'bg-red-100 text-red-800' : query.time > 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'"
                                      x-text="query.time + 'ms'"></span>
                                <span class="text-sm text-gray-500" x-text="query.type"></span>
                            </div>
                            <div class="bg-gray-100 p-4 rounded-lg">
                                <pre class="text-sm text-gray-800 whitespace-pre-wrap" x-text="query.sql"></pre>
                            </div>
                            <div x-show="query.params" class="mt-3 text-sm text-gray-600">
                                <strong>Parameters:</strong> <span x-text="query.params"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <script>
        function logProfiler() {
            return {
                activeTab: 'logs',
                logFilter: {
                    level: '',
                    date: '',
                    search: ''
                },
                logs: [
                    {
                        id: 1,
                        level: 'ERROR',
                        timestamp: '2024-07-10 10:30:15',
                        file: 'application/controllers/Home.php:45',
                        message: 'Database connection failed',
                        context: 'SQLSTATE[HY000] [2002] Connection refused'
                    },
                    {
                        id: 2,
                        level: 'WARNING',
                        timestamp: '2024-07-10 10:29:32',
                        file: 'application/models/User_model.php:123',
                        message: 'Deprecated function used: mysql_query()',
                        context: 'Use mysqli or PDO instead'
                    },
                    {
                        id: 3,
                        level: 'INFO',
                        timestamp: '2024-07-10 10:28:45',
                        file: 'application/controllers/Auth.php:78',
                        message: 'User logged in successfully',
                        context: 'User ID: 1234, IP: 192.168.1.100'
                    },
                    {
                        id: 4,
                        level: 'DEBUG',
                        timestamp: '2024-07-10 10:27:12',
                        file: 'application/libraries/Session.php:234',
                        message: 'Session data loaded from database',
                        context: 'Session ID: abc123def456'
                    }
                ],
                profilerData: {
                    executionTime: 245.7,
                    memoryUsage: 12.4,
                    queryCount: 15,
                    filesLoaded: 67,
                    peakMemory: 15.2,
                    currentMemory: 12.4,
                    benchmarks: [
                        { name: 'Loading Time', time: 23.4 },
                        { name: 'Database Connection', time: 45.2 },
                        { name: 'Controller Execution', time: 156.8 },
                        { name: 'View Rendering', time: 20.3 }
                    ]
                },
                queryStats: {
                    total: 15,
                    avgTime: 12.3,
                    slowest: 89.5
                },
                queries: [
                    {
                        time: 89.5,
                        type: 'SELECT',
                        sql: 'SELECT u.*, p.name as profile_name FROM users u LEFT JOIN profiles p ON u.id = p.user_id WHERE u.active = 1 AND u.created_at > DATE_SUB(NOW(), INTERVAL 30 DAY) ORDER BY u.created_at DESC LIMIT 50',
                        params: 'No parameters'
                    },
                    {
                        time: 23.1,
                        type: 'INSERT',
                        sql: 'INSERT INTO user_sessions (user_id, session_id, ip_address, user_agent, created_at) VALUES (?, ?, ?, ?, NOW())',
                        params: '[1234, "abc123def456", "192.168.1.100", "Mozilla/5.0..."]'
                    },
                    {
                        time: 8.7,
                        type: 'UPDATE',
                        sql: 'UPDATE users SET last_login = NOW(), login_count = login_count + 1 WHERE id = ?',
                        params: '[1234]'
                    }
                ],
                
                get filteredLogs() {
                    let filtered = this.logs;
                    
                    if (this.logFilter.level) {
                        filtered = filtered.filter(log => log.level === this.logFilter.level);
                    }
                    
                    if (this.logFilter.search) {
                        filtered = filtered.filter(log => 
                            log.message.toLowerCase().includes(this.logFilter.search.toLowerCase()) ||
                            log.file.toLowerCase().includes(this.logFilter.search.toLowerCase())
                        );
                    }
                    
                    return filtered;
                },
                
                getLogClass(level) {
                    const classes = {
                        'ERROR': 'border-l-4 border-red-500',
                        'WARNING': 'border-l-4 border-yellow-500',
                        'INFO': 'border-l-4 border-blue-500',
                        'DEBUG': 'border-l-4 border-gray-500'
                    };
                    return classes[level] || '';
                },
                
                getLevelClass(level) {
                    const classes = {
                        'ERROR': 'bg-red-100 text-red-800',
                        'WARNING': 'bg-yellow-100 text-yellow-800',
                        'INFO': 'bg-blue-100 text-blue-800',
                        'DEBUG': 'bg-gray-100 text-gray-800'
                    };
                    return classes[level] || 'bg-gray-100 text-gray-800';
                },
                
                applyFilters() {
                    // Filter logic is handled by the computed property
                    console.log('Filters applied');
                },
                
                refreshLogs() {
                    // Simulate API call to refresh logs
                    console.log('Refreshing logs...');
                },
                
                clearLogs() {
                    if (confirm('Are you sure you want to clear all logs?')) {
                        this.logs = [];
                    }
                },
                
                toggleLogDetails(logId) {
                    console.log('Toggling details for log:', logId);
                }
            }
        }
    </script>
</body>
</html>