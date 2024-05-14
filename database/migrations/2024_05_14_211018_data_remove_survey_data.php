<?php

use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('survey_field_submission');
        Schema::dropIfExists('survey_submissions');
        Schema::dropIfExists('survey_authentications');
        Schema::dropIfExists('survey_fields');
        Schema::dropIfExists('survey_steps');
        Schema::dropIfExists('surveys');
    }
};
