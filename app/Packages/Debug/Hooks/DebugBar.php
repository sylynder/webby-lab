<?php

/**
 * A Simple DebugBar For Webby
 */
class DebugBar
{
    /**
     * Initialize Profiler
     *
     * @return void
     */
    public function initProfiler()
    {
        if (ENVIRONMENT=='development') {
        	app()->output->enable_profiler(true);
        }
    }

    public function showDebugView()
    {
    
        if (ENVIRONMENT !='development' || app()->input->is_ajax_request()) {
        	echo $output= app()->output->get_output();
        } else {

        	$output = app()->output->get_output();

            app()->load->library('profiler');

            // $debugger = app()->use->view("debug-bar", '', true);
            $debugger = view("Debug.debug-bar", '', true);

            if (preg_match("|</body>.*?</html>|is", $output)) {
                $output  = preg_replace("|</body>.*?</html>|is", '', $output);
                $output .= app()->profiler->run();
                $output .= $debugger;
                $output .= '</body></html>';
            } else {
                $output .= app()->profiler->run().$debugger;
            }

        	echo $output;	
        }
    }
}
