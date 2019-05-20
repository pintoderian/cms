<?php

namespace App\Http\Controllers\Core\Logs;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Arcanedev\LogViewer\Http\Controllers\LogViewerController;

class SystemController extends LogViewerController
{
    public function list_logs(Request $request){
        $stats   = $this->logViewer->statsTable();
        $headers = $stats->header();
        $rows    = $this->paginate($stats->rows(), $request);

        $arr = [];
        $cont = 0;
        foreach($headers as $key => $item){
            $arr[$cont]["title"] = $item;
            $icon = preg_match('~"(.*?)"~', log_styler()->icon($key), $output);
            $arr[$cont]["icon"] = $output[1];
            $arr[$cont]["key"] = $key;
            $arr[$cont]["class"] = $key == 'date' ? 'text-left' : 'text-center';
            $cont++;
        }
        return [
            'headers' => $arr,
            'rows' => $rows
        ];
    }

    public function download_log_date($fecha){
        return $this->download($fecha);
    }

    public function view_log($date, $level, Request $request){
        $log     = $this->getLogOrFail($date);
        $query   = $request->get('query');
        $levels  = $this->logViewer->levelsNames();
        $entries = $log->entries($level)->paginate($this->perPage);
        $html = '';
        if($entries->hasPages()){
            $html = "<div class='card-header text-right'><span class='badge badge-info'>Página {$entries->currentPage()} de {$entries->lastPage()}</span></div>";
        }
        $items = $log->tree(true);
        foreach($items as $level => $item) {
            $items[$level] = array_merge($item, [
                'icon' => log_styler()->icon($level)
            ]);
        }
        $entradas = convert_from_latin1_to_utf8_json($entries);
        $result =  [
            'level' => $level,
            'query' => $query,
            'levels' => $levels,
            'entries' => $entradas,
            'logs' =>  $items,
            'info' => [
                'path' => $log->getPath(),
                'total' => $entries->total(),
                'size' => $log->size(),
                'createdAt' => date('Y/m/d', strtotime($log->createdAt())),
                'updatedAt' => date('Y/m/d', strtotime($log->updatedAt()))
            ],
            'header' => $html,
            
        ];
        return JsonResponse::create($result, 200, array('Content-Type'=>'application/json; charset=utf-8' )); 
    }
}
