<?php

namespace Base\Helpers;

use Base\Cache\Cache;

class Configurator 
{
    private static $cacheFile = null;
    
    public static function loadAllConfigs($useCache = true) 
    {
        self::$cacheFile = WRITABLEPATH . 'cache/config_cache.php';
        
        // Try to load from cache if enabled
        if ($useCache && self::isCacheValid()) {
            $cachedConfig = include self::$cacheFile;
            // Merge cached config into global $config
            global $config;
            $config = isset($config) ? array_merge($config, $cachedConfig) : $cachedConfig;
            return;
        }
        
        // Load fresh configs
        $mergedConfig = [];
        $files = glob(ROOTPATH . "config" . DIRECTORY_SEPARATOR . "*.php");
        
        $exclude = [
            'autoload', 'constants', 'database', 'hooks',
            'profiler', 'commands'
        ];
        
        foreach ($files as $file) {
            $skip = false;
            foreach ($exclude as $name) {
                if (stripos($file, $name) !== false) {
                    $skip = true;
                    break;
                }
            }
            
            if (!$skip) {
                $tempConfig = [];
                require_once $file;
                $mergedConfig = array_merge($mergedConfig, $tempConfig);
                unset($tempConfig);
            }
        }
        
        // Cache the config if caching is enabled
        if ($useCache) {
            self::cacheConfig($mergedConfig);
        }
        
        // Merge into global config
        global $config;
        $config = isset($config) ? array_merge($config, $mergedConfig) : $mergedConfig;
    }
    
    private static function isCacheValid() 
    {
        if (!file_exists(self::$cacheFile)) {
            return false;
        }
        
        $cacheTime = filemtime(self::$cacheFile);
        $configDir = ROOTPATH . "config" . DIRECTORY_SEPARATOR;
        
        // Check if any config file is newer than cache
        foreach (glob($configDir . "*.php") as $file) {
            if (filemtime($file) > $cacheTime) {
                return false;
            }
        }
        
        return true;
    }
    
    private static function cacheConfig(array $config) 
    {
        $cacheDir = dirname(self::$cacheFile);
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        
        $cacheContent = "<?php\n// Generated: " . date('Y-m-d H:i:s') . "\nreturn " . var_export($config, true) . ";\n";
        file_put_contents(self::$cacheFile, $cacheContent, LOCK_EX);
    }
}
