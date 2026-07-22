<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class QuizLock
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is currently in a quiz session
        $quizId = session('active_quiz_id');
        
        if ($quizId) {
            $deadlineKey = 'quiz_deadline_' . $quizId;
            
            if (session()->has($deadlineKey)) {
                $remainingSeconds = max(0, session($deadlineKey)->getTimestamp() - now()->getTimestamp());
                
                // If time is up, clear the lock
                if ($remainingSeconds <= 0) {
                    session()->forget('active_quiz_id');
                    session()->forget($deadlineKey);
                } else {
                    // User is in an active quiz - restrict other actions
                    $currentRoute = $request->route()->getName();
                    
                    // Allow only quiz-related routes
                    $allowedRoutes = [
                        'student.quizzes.attempt',
                        'student.quizzes.answer',
                        'student.quizzes.submit-all',
                        'student.quizzes.results'
                    ];
                    
                    if (!in_array($currentRoute, $allowedRoutes) && !str_contains($currentRoute, 'quiz')) {
                        // Redirect back to the quiz
                        return redirect()->route('student.quizzes.attempt', $quizId)
                            ->with('error', '⚠️ You must complete your active quiz first!');
                    }
                }
            }
        }
        
        return $next($request);
    }
}