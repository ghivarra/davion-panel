<?php 

namespace App\Libraries\Ghivarra;

/**
 * VueIgniter Library
 *
 * Created with love and proud by Ghivarra Senandika Rushdie
 *
 * @package Vuenized Library
 *
 * @var https://github.com/ghivarra
 * @var https://facebook.com/bcvgr
 * @var https://twitter.com/ghivarra
 *
**/

class VueIgniter
{
    protected $viewPath;
    protected $viewData = [];
    protected $rootView = 'DefaultView';

    //========================================================================================

    public function setRootView(string $rootViewPath): void
    {
        $this->rootView = $rootViewPath;
    }

    //========================================================================================
    
    public function getAssets(): array
    {
        $assets = [
            'js'  => [],
            'css' => []
        ];

        if ($_ENV['VITE_APP_ENV'] === 'development')
        {
            // build main js url
            $mainJSUrl = "{$_ENV['VITE_ORIGIN']}/{$_ENV['VITE_RESOURCES_DIR']}/main.js";

            // we didn't used ssl verifications so we can use self signed ssl in localhost environment such as laragon etc.
            // make sure you only develop your app in localhost so MITM attack is not an issue
            $mainJS = file_get_contents($mainJSUrl, false, stream_context_create([
                'ssl' => [
                    'verify_peer'      => false,
                    'verify_peer_name' => false
                ]
            ]));

            if (empty($mainJS))
            {
                throw new \Exception('Make sure you run "npm run dev" command on your vue applications or check the environment such as origin, port, and host in your dotenv configuration');
            }

            // push main js url
            array_push($assets['js'], "<script type=\"module\" crossorigin src=\"{$mainJSUrl}\" defer></script>");

        } elseif ($_ENV['VITE_APP_ENV'] === 'production' || $_ENV['VITE_APP_ENV'] === 'testing') {
            
            $manifestPath = FCPATH . ".vite/manifest.json";

            if (is_file($manifestPath))
            {
                $manifest = json_decode(file_get_contents($manifestPath));

                foreach ($manifest as $asset):

                    // check file extension, css or js
                    $fileExtension = strrchr($asset->file, '.');

                    if ($fileExtension === '.js')
                    {
                        if (isset($asset->isEntry) && $asset->isEntry)
                        {
                            $mainJS = base_url($asset->file);

                            array_push($assets['js'], "<script type=\"module\" src=\"{$mainJS}\" crossorigin defer></script>");

                            if (isset($asset->css) && !empty($asset->css))
                            {
                                foreach ($asset->css as $css):

                                    $css = base_url($css);
                                    array_push($assets['css'], "<link href=\"{$css}\" rel=\"stylesheet\">");

                                endforeach;
                            }
                        }
                    }

                endforeach;

            } else {

                throw new \Exception('Please run "npm run build" command on your vue applications');
            }
        }

        // return assets
        return $assets;
    }

    //========================================================================================

    public function getPageData(): string
    {
        return json_encode([
            'view' => $this->viewPath,
            'data' => $this->viewData
        ]);
    }

    //========================================================================================

    public function render(string $viewPath, array $data = [], string $noscriptMessage = 'You need to enable javascript in your browser to access this page'): string
    {
        // set view data
        if (!empty($data))
        {
            $this->viewData = $data;
        }

        $this->viewPath = $viewPath;
        $view = view($this->rootView, [
            'app'  => $this,
            'data' => $data
        ]);

        $assets  = $this->getAssets();
        $styles  = "\t";
        $scripts = "";

        // inject css into head
        foreach ($assets['css'] as $style):

            $styles .= $style . "\n";

        endforeach;

        $view = str_replace('</head>', $styles . '</head>', $view);

        // inject js into just before head
        foreach ($assets['js'] as $script):

            $scripts .= $script . "\n";

        endforeach;

        $view = str_replace('</head>', $scripts . '</head>', $view);

        // inject noscript
        $view = str_replace('<body>', "<body>\n\t<noscript>{$noscriptMessage}</noscript>", $view);

        // return
        return $view;
    }

    //========================================================================================
}