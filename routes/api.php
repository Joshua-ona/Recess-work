<?php
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\DiscussionController;
use App\Http\Controllers\Api\ReplyController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\GroupMessageController;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\Api\PrivateCommController;
use App\Http\Controllers\Api\NotificationController;

Route::post('/login',[AuthController::class,'login']);
Route::middleware('auth:sanctum')->get('/profile', function(Request $request){

    return response()->json([
        'user'=>$request->user()
    ]);

});
Route::middleware('auth:sanctum')->get('/profile', function(Request $request){

    return response()->json([
        'user'=>[
            'id'=>$request->user()->id,
            'first_name'=>$request->user()->first_name,
            'last_name'=>$request->user()->last_name,
            'email'=>$request->user()->email,
            'role'=>$request->user()->role
        ]
    ]);

});

Route::middleware('auth:sanctum')->group(function(){

    Route::apiResource(
        'discussions',
        DiscussionController::class
    );

});
Route::middleware('auth:sanctum')->group(function(){

    Route::get(
        '/discussions/{discussion}/replies',
        [ReplyController::class,'index']
    );


    Route::post(
        '/discussions/{discussion}/replies',
        [ReplyController::class,'store']
    );


    Route::delete(
        '/replies/{reply}',
        [ReplyController::class,'destroy']
    );

    Route::get('/groups', [GroupController::class,'index']);
Route::get('/groups/{group}', [GroupController::class,'show']);
Route::post('/groups', [GroupController::class,'store']);

Route::post(
    '/groups/{group}/join',
    [GroupController::class,'join']
);

Route::get(
    '/groups/{group}/members',
    [GroupController::class,'members']
);

Route::get(
    '/groups/{group}/messages',
    [GroupMessageController::class, 'index']
);

Route::post(
    '/groups/{group}/messages',
    [GroupMessageController::class, 'store']
);
Route::get('/quizzes',[QuizController::class,'index']);

Route::get('/quizzes/{quiz}',[QuizController::class,'show']);

Route::get(
    '/quizzes/{quiz}/questions',
    [QuizController::class,'questions']
);

Route::post(
    '/quizzes/{quiz}/submit',
    [QuizController::class,'submit']
);

Route::get(
    '/my-submissions',
    [QuizController::class,'mySubmissions']
);
Route::get(
    '/private-comms/users',
    [PrivateCommController::class,'users']
);

Route::get(
    '/private-comms/{user}',
    [PrivateCommController::class,'conversation']
);

Route::post(
    '/private-comms/{user}',
    [PrivateCommController::class,'send']
);
});
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/notifications',
        [NotificationController::class,'index']);

    Route::get('/notifications/count',
        [NotificationController::class,'count']);

});