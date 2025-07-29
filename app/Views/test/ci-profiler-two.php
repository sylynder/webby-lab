<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeIgniter 3 Advanced Log Profiler</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        .log-error { @apply bg-red-50 border-red-200 text-red-800; }
        .log-warning { @apply bg-yellow-50 border-yellow-200 text-yellow-800; }
        .log-info { @apply bg-blue-50 border-blue-200 text-blue-800; }
        .log-debug { @apply bg-gray-50 border-gray-200 text-gray-800; }
        .log-security { @apply bg-purple-50 border-purple-200 text-purple-800; }
        .profiler-section { @apply bg-gradient-to-r from-purple-600 to-blue-600 text-white; }
        .notification { @apply fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg; }
        .notification-success { @apply bg-green-500 text-white; }
        .notification-error { @apply bg-red-500 text-white; }
        .notification-warning { @apply bg-yellow-500 text-white; }
        .pulse-dot { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .5; }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen" x-data="logProfiler()">
    <!-- Notifications -->
    <div x-show="notification.show" x-cloak 
         class="notification" 
         :class="notification.type"
         x-transition:enter="transform ease-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transform ease-in duration-300"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i :class="notification.icon" class="mr-2"></i>
                <span x-text="notification.message"></span>
            </div>
            <button @click="notification.show = false" class="ml-4 text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- Performance Alerts Modal -->
    <div x-show="showAlertsModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div>
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                        <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Performance Alert Settings</h3>
                        <div class="mt-2">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Slow Query Threshold (ms)</label>
                                    <input x-model="alertSettings.slowQueryThreshold" type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Memory Usage Threshold (MB)</label>
                                    <input x-model="alertSettings.memoryThreshold" type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Execution Time Threshold (ms)</label>
                                    <input x-model="alertSettings.executionTimeThreshold" type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                    <button @click="saveAlertSettings()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm">
                        Save Settings
                    </button>
                    <button @click="showAlertsModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="bg-white shadow-lg border-b border-gray-200">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-fire text-orange-500 text-2xl"></i>
                    <h1 class="text-2xl font-bold text-gray-800">CodeIgniter 3 Advanced Log Profiler</h1>
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">PHP 8.3</span>
                    <div class="flex items-center space-x-1">
                        <div class="w-2 h-2 bg-green-500 rounded-full pulse-dot"></div>
                        <span class="text-xs text-gray-600">Live</span>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button @click="showAlertsModal = true" class="px-3 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors text-sm">
                        <i class="fas fa-bell mr-1"></i>Alerts
                    </button>
                    <button @click="exportLogs()" class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm">
                        <i class="fas fa-download mr-1"></i>Export
                    </button>
                    <button @click="toggleLiveUpdates()" class="px-3 py-2 text-white rounded-lg transition-colors text-sm"
                            :class="liveUpdates ? 'bg-red-600 hover:bg-red-700' : 'bg-gray-600 hover:bg-gray-700'">
                        <i class="fas fa-power-off mr-1"></i>
                        <span x-text="liveUpdates ? 'Stop Live' : 'Start Live'"></span>
                    </button>
                    <button @click="refreshLogs()" class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                        <i class="fas fa-refresh mr-1"></i>Refresh
                    </button>
                    <button @click="clearLogs()" class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm">
                        <i class="fas fa-trash mr-1"></i>Clear
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8">
        <!-- Tab Navigation -->
        <div class="flex flex-wrap space-x-1 bg-white p-1 rounded-lg shadow mb-6">
            <button @click="activeTab = 'logs'" 
                    :class="activeTab === 'logs' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-gray-800'"
                    class="px-4 py-2 rounded-md font-medium transition-colors text-sm">
                <i class="fas fa-file-text mr-1"></i>Logs
            </button>
            <button @click="activeTab = 'profiler'" 
                    :class="activeTab === 'profiler' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-gray-800'"
                    class="px-4 py-2 rounded-md font-medium transition-colors text-sm">
                <i class="fas fa-tachometer-alt mr-1"></i>Profiler
            </button>
            <button @click="activeTab = 'queries'" 
                    :class="activeTab === 'queries' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-gray-800'"
                    class="px-4 py-2 rounded-md font-medium transition-colors text-sm">
                <i class="fas fa-database mr-1"></i>Queries
            </button>
            <button @click="activeTab = 'security'" 
                    :class="activeTab === 'security' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-gray-800'"
                    class="px-4 py-2 rounded-md font-medium transition-colors text-sm">
                <i class="fas fa-shield-alt mr-1"></i>Security
            </button>
            <button @click="activeTab = 'analytics'" 
                    :class="activeTab === 'analytics' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-gray-800'"
                    class="px-4 py-2 rounded-md font-medium transition-colors text-sm">
                <i class="fas fa-chart-line mr-1"></i>Analytics
            </button>
        </div>

        <!-- Logs Tab -->
        <div x-show="activeTab === 'logs'" x-cloak class="space-y-6">
            <!-- Advanced Filters -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Advanced Filters</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Log Level</label>
                        <select x-model="logFilter.level" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Levels</option>
                            <option value="ERROR">Error</option>
                            <option value="WARNING">Warning</option>
                            <option value="INFO">Info</option>
                            <option value="DEBUG">Debug</option>
                            <option value="SECURITY">Security</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                        <input x-model="logFilter.dateFrom" type="datetime-local" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                        <input x-model="logFilter.dateTo" type="datetime-local" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">File Filter</label>
                        <select x-model="logFilter.file" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Files</option>
                            <option value="controllers">Controllers</option>
                            <option value="models">Models</option>
                            <option value="libraries">Libraries</option>
                            <option value="helpers">Helpers</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input x-model="logFilter.search" type="text" placeholder="Search logs..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex items-end space-x-2">
                        <button @click="applyFilters()" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                            <i class="fas fa-filter mr-1"></i>Apply
                        </button>
                        <button @click="clearFilters()" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                            <i class="fas fa-eraser mr-1"></i>Clear
                        </button>
                    </div>
                </div>
            </div>

            <!-- Log Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-100 rounded-lg">
                            <i class="fas fa-exclamation-circle text-red-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-600">Errors</p>
                            <p class="text-lg font-semibold text-red-600" x-text="logSummary.errors"></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-600">Warnings</p>
                            <p class="text-lg font-semibold text-yellow-600" x-text="logSummary.warnings"></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <i class="fas fa-info-circle text-blue-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-600">Info</p>
                            <p class="text-lg font-semibold text-blue-600" x-text="logSummary.info"></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="flex items-center">
                        <div class="p-2 bg-gray-100 rounded-lg">
                            <i class="fas fa-bug text-gray-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-600">Debug</p>
                            <p class="text-lg font-semibold text-gray-600" x-text="logSummary.debug"></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <i class="fas fa-shield-alt text-purple-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-600">Security</p>
                            <p class="text-lg font-semibold text-purple-600" x-text="logSummary.security"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Log Entries -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">Log Entries</h3>
                        <div class="flex items-center space-x-4">
                            <p class="text-sm text-gray-600">Showing <span x-text="filteredLogs.length"></span> of <span x-text="logs.length"></span> entries</p>
                            <div class="flex items-center space-x-2">
                                <label class="text-sm text-gray-600">Per page:</label>
                                <select x-model="pagination.perPage" class="px-2 py-1 border border-gray-300 rounded text-sm">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="max-h-96 overflow-y-auto">
                    <template x-for="log in paginatedLogs" :key="log.id">
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
                                        <span x-show="log.ip" class="text-sm text-gray-500" x-text="'IP: ' + log.ip"></span>
                                    </div>
                                    <p class="text-gray-800 font-medium mb-1" x-text="log.message"></p>
                                    <div x-show="log.context" class="text-sm text-gray-600 bg-gray-100 p-2 rounded mt-2">
                                        <pre class="whitespace-pre-wrap" x-text="log.context"></pre>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button @click="toggleLogDetails(log.id)" class="text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                    <button @click="copyLogToClipboard(log)" class="text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-600">
                            Showing <span x-text="(pagination.currentPage - 1) * pagination.perPage + 1"></span> to 
                            <span x-text="Math.min(pagination.currentPage * pagination.perPage, filteredLogs.length)"></span> of 
                            <span x-text="filteredLogs.length"></span> results
                        </div>
                        <div class="flex space-x-2">
                            <button @click="pagination.currentPage = Math.max(1, pagination.currentPage - 1)" 
                                    :disabled="pagination.currentPage === 1"
                                    class="px-3 py-1 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 disabled:opacity-50">
                                Previous
                            </button>
                            <button @click="pagination.currentPage = Math.min(totalPages, pagination.currentPage + 1)" 
                                    :disabled="pagination.currentPage === totalPages"
                                    class="px-3 py-1 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 disabled:opacity-50">
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profiler Tab -->
        <div x-show="activeTab === 'profiler'" x-cloak class="space-y-6">
            <!-- Performance Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-lg shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100">Execution Time</p>
                            <p class="text-2xl font-bold" x-text="profilerData.executionTime + 'ms'"></p>
                        </div>
                        <i class="fas fa-clock text-2xl opacity-80"></i>
                    </div>
                    <div class="mt-2">
                        <div class="flex items-center text-sm">
                            <i :class="profilerData.executionTime > alertSettings.executionTimeThreshold ? 'fas fa-arrow-up text-red-300' : 'fas fa-arrow-down text-green-300'" class="mr-1"></i>
                            <span x-text="profilerData.executionTime > alertSettings.executionTimeThreshold ? 'Above threshold' : 'Normal'"></span>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-lg shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100">Memory Usage</p>
                            <p class="text-2xl font-bold" x-text="profilerData.memoryUsage + 'MB'"></p>
                        </div>
                        <i class="fas fa-memory text-2xl opacity-80"></i>
                    </div>
                    <div class="mt-2">
                        <div class="flex items-center text-sm">
                            <i :class="profilerData.memoryUsage > alertSettings.memoryThreshold ? 'fas fa-arrow-up text-red-300' : 'fas fa-arrow-down text-green-300'" class="mr-1"></i>
                            <span x-text="profilerData.memoryUsage > alertSettings.memoryThreshold ? 'High usage' : 'Normal'"></span>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-6 rounded-lg shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100">DB Queries</p>
                            <p class="text-2xl font-bold" x-text="profilerData.queryCount"></p>
                        </div>
                        <i class="fas fa-database text-2xl opacity-80"></i>
                    </div>
                    <div class="mt-2">
                        <div class="flex items-center text-sm">
                            <i class="fas fa-clock text-purple-300 mr-1"></i>
                            <span x-text="queryStats.avgTime + 'ms avg'"></span>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white p-6 rounded-lg shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-100">Files Loaded</p>
                            <p class="text-2xl font-bold" x-text="profilerData.filesLoaded"></p>
                        </div>
                        <i class="fas fa-file text-2xl opacity-80"></i>
                    </div>
                    <div class="mt-2">
                        <div class="flex items-center text-sm">
                            <i class="fas fa-hdd text-orange-300 mr-1"></i>
                            <span x-text="profilerData.autoloadTime + 'ms'"></span>
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
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <div>
                                    <span class="font-medium text-gray-700" x-text="benchmark.name"></span>
                                    <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                        <div class="bg-blue-500 h-2 rounded-full" :style="`width: ${(benchmark.time / Math.max(...profilerData.benchmarks.map(b => b.time))) * 100}%`"></div>
                                    </div>
                                </div>
                                <span class="text-sm text-gray-500 ml-4" x-text="benchmark.time + 'ms'"></span>
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
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Peak Memory</span>
                                <span class="font-semibold" x-text="profilerData.peakMemory + 'MB'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Current Memory</span>
                                <span class="font-semibold" x-text="profilerData.currentMemory + 'MB'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Memory Limit</span>
                                <span class="font-semibold" x-text="profilerData.memoryLimit + 'MB'"></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-gradient-to-r from-green-500 to-yellow-500 h-3 rounded-full transition-all duration-300" 
                                     :style="`width: ${(profilerData.currentMemory / profilerData.memoryLimit) * 100}%`"></div>
                            </div>
                            <div class="text-sm text-gray-500 text-center">
                                <span x-text="Math.round((profilerData.currentMemory / profilerData.memoryLimit) * 100) + '% of limit used'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Load -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="profiler-section px-6 py-4">
                        <h3 class="text-lg font-semibold">System Load</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600" x-text="profilerData.cpuUsage + '%'"></div>
                                <div class="text-sm text-gray-600">CPU Usage</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600" x-text="profilerData.diskUsage + '%'"></div>
                                <div class="text-sm text-gray-600">Disk Usage</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Errors -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="profiler-section px-6 py-4">
                        <h3 class="text-lg font-semibold">Recent Errors</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <template x-for="error in profilerData.recentErrors" :key="error.id">
                                <div class="flex items-start space-x-3 p-3 bg-red-50 rounded border-l-4 border-red-500">
                                    <i class="fas fa-exclamation-triangle text-red-600 mt-1"></i>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-red-800" x-text="error.message"></p>
                                        <p class="text-xs text-red-600" x-text="error.file + ':' + error.line"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Queries Tab -->
        <div x-show="activeTab === 'queries'" x-cloak class="space-y-6">
            <!-- Query Statistics -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Query Performance Analysis</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
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
                    <div class="text-center">
                        <div class="text-3xl font-bold text-yellow-600" x-text="queryStats.slowQueries"></div>
                        <div class="text-sm text-gray-600">Slow Queries</div>
                    </div>
                </div>
            </div>

            <!-- Query Type Distribution -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Query Type Distribution</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <template x-for="(count, type) in queryStats.byType" :key="type">
                        <div class="text-center p-4 bg-gray-50 rounded">
                            <div class="text-2xl font-bold text-gray-700" x-text="count"></div>
                            <div class="text-sm text-gray-600 uppercase" x-text="type"></div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Query Filters -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Query Filters</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Query Type</label>
                        <select x-model="queryFilter.type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Types</option>
                            <option value="SELECT">SELECT</option>
                            <option value="INSERT">INSERT</option>
                            <option value="UPDATE">UPDATE</option>
                            <option value="DELETE">DELETE</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Min Execution Time (ms)</label>
                        <input x-model="queryFilter.minTime" type="number" placeholder="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex items-end">
                        <button @click="applyQueryFilters()" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            <i class="fas fa-filter mr-2"></i>Apply Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Query List -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">Database Queries</h3>
                        <button @click="optimizeQueries()" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                            <i class="fas fa-magic mr-2"></i>Analyze & Optimize
                        </button>
                    </div>
                </div>
                <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                    <template x-for="(query, index) in filteredQueries" :key="index">
                        <div class="p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex items-center space-x-3">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full"
                                          :class="query.time > alertSettings.slowQueryThreshold ? 'bg-red-100 text-red-800' : query.time > 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'"
                                          x-text="query.time + 'ms'"></span>
                                    <span class="text-sm text-gray-500" x-text="query.type"></span>
                                    <span x-show="query.cached" class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">Cached</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button @click="explainQuery(query)" class="text-blue-600 hover:text-blue-800 text-sm">
                                        <i class="fas fa-info-circle mr-1"></i>Explain
                                    </button>
                                    <button @click="copyQueryToClipboard(query)" class="text-gray-600 hover:text-gray-800 text-sm">
                                        <i class="fas fa-copy mr-1"></i>Copy
                                    </button>
                                </div>
                            </div>
                            <div class="bg-gray-100 p-4 rounded-lg">
                                <pre class="text-sm text-gray-800 whitespace-pre-wrap" x-text="query.sql"></pre>
                            </div>
                            <div class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div x-show="query.params">
                                    <strong class="text-gray-700">Parameters:</strong>
                                    <span class="text-gray-600" x-text="query.params"></span>
                                </div>
                                <div x-show="query.rowsAffected">
                                    <strong class="text-gray-700">Rows Affected:</strong>
                                    <span class="text-gray-600" x-text="query.rowsAffected"></span>
                                </div>
                                <div x-show="query.caller">
                                    <strong class="text-gray-700">Called From:</strong>
                                    <span class="text-gray-600" x-text="query.caller"></span>
                                </div>
                            </div>
                            <div x-show="query.suggestions" class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded">
                                <strong class="text-yellow-800">Optimization Suggestions:</strong>
                                <ul class="text-sm text-yellow-700 mt-1">
                                    <template x-for="suggestion in query.suggestions" :key="suggestion">
                                        <li class="flex items-start">
                                            <i class="fas fa-lightbulb text-yellow-600 mt-1 mr-2"></i>
                                            <span x-text="suggestion"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Security Tab -->
        <div x-show="activeTab === 'security'" x-cloak class="space-y-6">
            <!-- Security Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-gradient-to-r from-red-500 to-red-600 text-white p-6 rounded-lg shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-red-100">Failed Logins</p>
                            <p class="text-2xl font-bold" x-text="securityStats.failedLogins"></p>
                        </div>
                        <i class="fas fa-shield-alt text-2xl opacity-80"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white p-6 rounded-lg shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-yellow-100">Suspicious IPs</p>
                            <p class="text-2xl font-bold" x-text="securityStats.suspiciousIPs"></p>
                        </div>
                        <i class="fas fa-exclamation-triangle text-2xl opacity-80"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-6 rounded-lg shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100">Blocked Requests</p>
                            <p class="text-2xl font-bold" x-text="securityStats.blockedRequests"></p>
                        </div>
                        <i class="fas fa-ban text-2xl opacity-80"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-lg shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100">Security Score</p>
                            <p class="text-2xl font-bold" x-text="securityStats.score + '%'"></p>
                        </div>
                        <i class="fas fa-check-circle text-2xl opacity-80"></i>
                    </div>
                </div>
            </div>

            <!-- Security Events -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-800">Recent Security Events</h3>
                </div>
                <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                    <template x-for="event in securityEvents" :key="event.id">
                        <div class="p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start space-x-3">
                                    <div class="p-2 rounded-full"
                                         :class="event.severity === 'high' ? 'bg-red-100 text-red-600' : event.severity === 'medium' ? 'bg-yellow-100 text-yellow-600' : 'bg-blue-100 text-blue-600'">
                                        <i :class="event.severity === 'high' ? 'fas fa-exclamation-triangle' : event.severity === 'medium' ? 'fas fa-exclamation-circle' : 'fas fa-info-circle'"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900" x-text="event.type"></p>
                                        <p class="text-sm text-gray-600" x-text="event.description"></p>
                                        <div class="flex items-center space-x-4 mt-2 text-sm text-gray-500">
                                            <span x-text="event.timestamp"></span>
                                            <span x-text="'IP: ' + event.ip"></span>
                                            <span x-text="'User Agent: ' + event.userAgent"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button @click="blockIP(event.ip)" class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                                        Block IP
                                    </button>
                                    <button @click="investigateEvent(event)" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                        Investigate
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- IP Blocklist -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">IP Blocklist</h3>
                        <button @click="showAddIPModal = true" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Add IP
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <template x-for="blockedIP in blockedIPs" :key="blockedIP.ip">
                            <div class="flex items-center justify-between p-3 bg-red-50 border border-red-200 rounded">
                                <div>
                                    <div class="font-medium text-red-800" x-text="blockedIP.ip"></div>
                                    <div class="text-sm text-red-600" x-text="blockedIP.reason"></div>
                                </div>
                                <button @click="unblockIP(blockedIP.ip)" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytics Tab -->
        <div x-show="activeTab === 'analytics'" x-cloak class="space-y-6">
            <!-- Analytics Overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">Log Trends (24h)</h3>
                    <canvas id="logTrendsChart" width="300" height="200"></canvas>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">Error Distribution</h3>
                    <canvas id="errorDistributionChart" width="300" height="200"></canvas>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">Performance Metrics</h3>
                    <canvas id="performanceChart" width="300" height="200"></canvas>
                </div>
            </div>

            <!-- Detailed Analytics -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">Top Error Sources</h3>
                    <div class="space-y-3">
                        <template x-for="source in analytics.topErrorSources" :key="source.file">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                <div>
                                    <div class="font-medium text-gray-800" x-text="source.file"></div>
                                    <div class="text-sm text-gray-600" x-text="source.count + ' errors'"></div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-red-600" x-text="Math.round((source.count / analytics.totalErrors) * 100) + '%'"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">Slowest Endpoints</h3>
                    <div class="space-y-3">
                        <template x-for="endpoint in analytics.slowestEndpoints" :key="endpoint.path">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                <div>
                                    <div class="font-medium text-gray-800" x-text="endpoint.path"></div>
                                    <div class="text-sm text-gray-600" x-text="endpoint.requests + ' requests'"></div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-yellow-600" x-text="endpoint.avgTime + 'ms'"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function logProfiler() {
            return {
                activeTab: 'logs',
                liveUpdates: false,
                showAlertsModal: false,
                showAddIPModal: false,
                websocket: null,
                
                // Notifications
                notification: {
                    show: false,
                    type: 'notification-success',
                    message: '',
                    icon: 'fas fa-check-circle'
                },
                
                // Alert Settings
                alertSettings: {
                    slowQueryThreshold: 100,
                    memoryThreshold: 50,
                    executionTimeThreshold: 1000
                },
                
                // Log Filters
                logFilter: {
                    level: '',
                    dateFrom: '',
                    dateTo: '',
                    file: '',
                    search: ''
                },
                
                // Query Filters
                queryFilter: {
                    type: '',
                    minTime: ''
                },
                
                // Pagination
                pagination: {
                    currentPage: 1,
                    perPage: 25
                },
                
                // Sample Data
                logs: [
                    {
                        id: 1,
                        level: 'ERROR',
                        timestamp: '2024-07-10 15:30:15',
                        file: 'application/controllers/Home.php:45',
                        message: 'Database connection failed after 3 retry attempts',
                        context: 'SQLSTATE[HY000] [2002] Connection refused\nConnection string: mysql:host=localhost;dbname=ci_app',
                        ip: '192.168.1.100'
                    },
                    {
                        id: 2,
                        level: 'WARNING',
                        timestamp: '2024-07-10 15:29:32',
                        file: 'application/models/User_model.php:123',
                        message: 'Deprecated function used: mysql_query()',
                        context: 'Use mysqli or PDO instead. This function will be removed in PHP 8.0+',
                        ip: '192.168.1.101'
                    },
                    {
                        id: 3,
                        level: 'INFO',
                        timestamp: '2024-07-10 15:28:45',
                        file: 'application/controllers/Auth.php:78',
                        message: 'User logged in successfully',
                        context: 'User ID: 1234, Session ID: abc123def456',
                        ip: '192.168.1.102'
                    },
                    {
                        id: 4,
                        level: 'DEBUG',
                        timestamp: '2024-07-10 15:27:12',
                        file: 'application/libraries/Session.php:234',
                        message: 'Session data loaded from database',
                        context: 'Session ID: abc123def456, User ID: 1234',
                        ip: '192.168.1.103'
                    },
                    {
                        id: 5,
                        level: 'SECURITY',
                        timestamp: '2024-07-10 15:26:05',
                        file: 'application/controllers/Auth.php:45',
                        message: 'Multiple failed login attempts detected',
                        context: 'IP: 192.168.1.200, Username: admin, Attempts: 5',
                        ip: '192.168.1.200'
                    }
                ],
                
                profilerData: {
                    executionTime: 1245.7,
                    memoryUsage: 52.4,
                    queryCount: 15,
                    filesLoaded: 67,
                    peakMemory: 65.2,
                    currentMemory: 52.4,
                    memoryLimit: 128,
                    cpuUsage: 23.5,
                    diskUsage: 67.8,
                    autoloadTime: 12.3,
                    benchmarks: [
                        { name: 'Bootstrap Loading', time: 23.4 },
                        { name: 'Database Connection', time: 45.2 },
                        { name: 'Controller Execution', time: 156.8 },
                        { name: 'Model Processing', time: 89.3 },
                        { name: 'View Rendering', time: 67.2 },
                        { name: 'Output Compression', time: 8.1 }
                    ],
                    recentErrors: [
                        { id: 1, message: 'Undefined variable: user_data', file: 'Home.php', line: 45 },
                        { id: 2, message: 'Call to undefined method', file: 'User_model.php', line: 123 },
                        { id: 3, message: 'Division by zero', file: 'Calculator.php', line: 67 }
                    ]
                },
                
                queryStats: {
                    total: 15,
                    avgTime: 23.7,
                    slowest: 189.5,
                    slowQueries: 3,
                    byType: {
                        'SELECT': 8,
                        'INSERT': 3,
                        'UPDATE': 3,
                        'DELETE': 1
                    }
                },
                
                queries: [
                    {
                        time: 189.5,
                        type: 'SELECT',
                        sql: 'SELECT u.*, p.name as profile_name, COUNT(o.id) as order_count FROM users u LEFT JOIN profiles p ON u.id = p.user_id LEFT JOIN orders o ON u.id = o.user_id WHERE u.active = 1 AND u.created_at > DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY u.id ORDER BY u.created_at DESC LIMIT 50',
                        params: 'No parameters',
                        rowsAffected: 50,
                        caller: 'User_model.php:get_active_users()',
                        cached: false,
                        suggestions: [
                            'Add index on users.active column',
                            'Add index on users.created_at column',
                            'Consider pagination for large result sets'
                        ]
                    },
                    {
                        time: 67.3,
                        type: 'INSERT',
                        sql: 'INSERT INTO user_sessions (user_id, session_id, ip_address, user_agent, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())',
                        params: '[1234, "abc123def456", "192.168.1.100", "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"]',
                        rowsAffected: 1,
                        caller: 'Session.php:create_session()',
                        cached: false
                    },
                    {
                        time: 12.7,
                        type: 'UPDATE',
                        sql: 'UPDATE users SET last_login = NOW(), login_count = login_count + 1 WHERE id = ?',
                        params: '[1234]',
                        rowsAffected: 1,
                        caller: 'Auth.php:update_login_stats()',
                        cached: false
                    },
                    {
                        time: 5.2,
                        type: 'SELECT',
                        sql: 'SELECT * FROM settings WHERE setting_key = ?',
                        params: '["app_name"]',
                        rowsAffected: 1,
                        caller: 'Config.php:get_setting()',
                        cached: true
                    }
                ],
                
                securityStats: {
                    failedLogins: 12,
                    suspiciousIPs: 3,
                    blockedRequests: 45,
                    score: 85
                },
                
                securityEvents: [
                    {
                        id: 1,
                        type: 'Failed Login Attempt',
                        description: 'Multiple failed login attempts from same IP',
                        severity: 'high',
                        timestamp: '2024-07-10 15:30:15',
                        ip: '192.168.1.200',
                        userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                    },
                    {
                        id: 2,
                        type: 'SQL Injection Attempt',
                        description: 'Suspicious SQL patterns detected in request',
                        severity: 'high',
                        timestamp: '2024-07-10 15:28:45',
                        ip: '192.168.1.201',
                        userAgent: 'curl/7.68.0'
                    }
                ]

            }
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.10.2/cdn.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chart.js/3.7.1/chart.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
             Alpine.data('logProfiler', logProfiler);
             
        });
    </script>
</body>
</html> 
