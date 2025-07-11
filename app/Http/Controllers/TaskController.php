<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BitrixInstallation;
use App\Services\BitrixService;

class TaskController extends Controller
{
    public function showForm()
    {
        return view('task.form');
    }

    public function create(Request $request, BitrixService $bx)
    {
        $request->validate(['title'=>'required','description'=>'required']);
        try {
            // Вызываем сервис, который создаёт задачу
            $response = $bx->addTask($request->title, $request->description);

            // Сохраняем результат в сессию для вывода уведомления
            return back()->with('response', $response);

        } catch (\Exception $e) {
            // Если произошла ошибка — сохраняем её
            return back()->with('error', $e->getMessage());
        }
    }

//    public function create(Request $req, BitrixService $bx)
//    {
//        $req->validate(['title'=>'required','description'=>'required']);
//        $inst = $bx->ensureToken();
//        if (!$inst) return back()->with('error','Интеграция не настроена');
//
//        // Получаем ID пользователя
//        $urlUser = "https://{$inst->domain}/rest/user.current.json";
//        $ch = curl_init($urlUser);
//        curl_setopt_array($ch, [
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_POST => true,
//            CURLOPT_POSTFIELDS => http_build_query(['auth' => $inst->auth_id]),
//        ]);
//        $resU = curl_exec($ch);
//        curl_close($ch);
//        $dataU = json_decode($resU, true);
//        $userId = $dataU['result']['ID'] ?? null;
//        if (!$userId) return back()->with('error', 'Не удалось получить ID пользователя');
//
//        // Создаем задачу
//        $url = "https://{$inst->domain}/rest/tasks.task.add.json";
//        $post = http_build_query([
//            'auth' => $inst->auth_id,
//            'fields' => [
//                'TITLE'          => $req->input('title'),
//                'DESCRIPTION'    => $req->input('description'),
//                'RESPONSIBLE_ID' => $userId,
//            ],
//        ]);
//
//        $ch = curl_init($url);
//        curl_setopt_array($ch, [
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_POST => true,
//            CURLOPT_POSTFIELDS => $post,
//        ]);
//        $res = curl_exec($ch);
//        $err = curl_error($ch);
//        curl_close($ch);
//
//        $response = $err ? ['success' => false, 'error' => $err] : json_decode($res, true);
//        return back()->with('response', $response);
//    }

}
