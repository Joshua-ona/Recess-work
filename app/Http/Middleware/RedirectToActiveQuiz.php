<?php

namespace App\Http\Middleware;

use App\Models\Quiz;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectToActiveQuiz
{
    /**
     * Route names a student is still allowed to hit while a quiz is in progress.
     */
    protected array $allowedRouteNames = [
        'student.quizzes.attempt',
        'student.quizzes.answer',
        'student.quizzes.submit-all',
        'student.quizzes.results',
        'logout',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $activeQuizId = session('active_quiz_id');

        if ($activeQuizId) {
            $routeName = $request->route()?->getName();

            if (! in_array($routeName, $this->allowedRouteNames, true)) {
                $deadlineKey = 'quiz_deadline_' . $activeQuizId;
                $quiz = Quiz::find($activeQuizId);

                $stillActive = $quiz
                    && session()->has($deadlineKey)
                    && now()->lessThan(session($deadlineKey));

                if ($stillActive) {
                    return redirect()
                        ->route('student.quizzes.attempt', $activeQuizId)
                        ->with('error', 'You have a quiz in progress. Please finish or submit it before doing anything else.');
                }

                // Quiz is no longer active (expired / missing) — clear the lock.
                session()->forget('active_quiz_id');
            }
        }

        return $next($request);
    }
}