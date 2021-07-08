<?php

namespace App\Console\Commands;

use App\Mail\ServicesProfessionalsEmail;
use App\Models\Scheduling\Reserve;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ServicesProfessionalsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '4home:schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia la agenda a los profesionales por correo';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $tomorrow = Carbon::tomorrow();
            $day =$tomorrow->dayOfWeek;
            if($day == 0) {
                $day = 6;
            } else {
                $day--;
            }

            $reserves = Reserve::reservesProfessionals($tomorrow->toDateString(), $day);

            foreach($reserves as $reserve) {
                Mail::to($reserve->email)->send(
                    new ServicesProfessionalsEmail(
                        $reserve->fullname,
                        $reserve->reference,
                        $reserve->client,
                        $reserve->address,
                        $reserve->workingDay,
                    )
                );
            }
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'ServicesProfessionalsCommand', $e->getMessage()));
        }
    }
}
