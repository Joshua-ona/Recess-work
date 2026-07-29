<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
{
    Schema::table('user_scores', function (Blueprint $table) {
        if (!$this->constraintExists('user_scores_user_id_unique')) {
            $table->unique('user_id');
        }
    });
}

private function constraintExists(string $name): bool
{
    $result = DB::select("
        SELECT conname FROM pg_constraint WHERE conname = ?
    ", [$name]);
    return count($result) > 0;
}
};
