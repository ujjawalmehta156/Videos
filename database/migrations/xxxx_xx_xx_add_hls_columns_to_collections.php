<?php
// database/migrations/xxxx_xx_xx_add_hls_columns_to_collections.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHlsColumnsToCollections extends Migration
{
    public function up()
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->string('video_path')->nullable()->after('description');
            $table->string('hls_master_url')->nullable()->after('video_path');
            $table->integer('conversion_progress')->default(0)->after('hls_master_url');
        });
    }

    public function down()
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->dropColumn(['video_path', 'hls_master_url', 'conversion_progress']);
        });
    }
}
