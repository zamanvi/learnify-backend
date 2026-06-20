<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_type');
            $table->string('redrose_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('as_user');
            $table->boolean('status');
            $table->boolean('is_first')->default(true);
            $table->string('password');
            // profile
            $table->string('date')->nullable();
            $table->string('once')->nullable();
            $table->integer('points')->default(0);
            $table->string('bio')->nullable();
            $table->string('designation')->nullable();
            $table->string('birthday')->nullable();
            $table->string('gender')->nullable();
            $table->longText('about')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('upazila_id')->nullable();
            $table->string('city_id')->nullable();
            $table->string('division_id')->nullable();
            $table->string('country_id')->nullable();
            $table->string('company_name')->nullable();
            // social links
            $table->string('whatsapp')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('instagram')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('pinterest')->nullable();
            $table->string('tiktok')->nullable();
            $table->string('wechat')->nullable();
            // teacher
            $table->string('title')->nullable();
            $table->string('slug')->nullable()->unique();
            $table->longText('title_description')->nullable();
            $table->string('expected_salary')->nullable();
            $table->string('period_class')->nullable();
            $table->string('duration')->nullable();
            $table->string('place_of_learning')->nullable();
            $table->string('through_of_learning')->nullable();
            $table->string('tuition_type')->nullable();
            $table->string('tuition_class')->nullable();
            $table->string('tuition_subject')->nullable();
            $table->string('tuition_time')->nullable();
            $table->string('medium')->nullable();
            $table->string('status_for_tuition')->nullable();
            $table->string('about_teacher')->nullable();
            $table->string('about_teaching')->nullable();
            $table->string('degree')->nullable();
            $table->string('year')->nullable();
            $table->string('institute')->nullable();
            $table->string('group_subject')->nullable();
            $table->string('result')->nullable();
            $table->string('degree_proof')->nullable();
            $table->string('area_country')->nullable();
            $table->string('area_division')->nullable();
            $table->string('area_city')->nullable();
            $table->string('area_upazila')->nullable();
            $table->string('area_post_office')->nullable();
            $table->string('area_union')->nullable();
            $table->string('area_village')->nullable();
            $table->string('area_road_house')->nullable();
            $table->integer('update_status')->default(0);
            $table->string('remark')->nullable();
            // students
            $table->string('institution')->nullable();
            $table->string('class_department')->nullable();
            $table->string('roll')->nullable();
            $table->longText('about_student')->nullable();
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
