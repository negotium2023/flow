<?php

namespace App\Console\Commands;

use App\HelperFunction;
use App\Mail\TrialNotificationMail;
use App\Notification;
use App\OfficeUser;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TrialNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:trial';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify financial advisor if trial is about to expire';

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
     * @return mixed
     */
    public function handle()
    {
        $office_subscription = new HelperFunction();
        $user_offices = OfficeUser::get()->unique('office_id')->values();

        $notification = new Notification;

        foreach ($user_offices as $office){
            $subscription = $office_subscription->officeSubscription((int) $office->office_id);
            if ($subscription["subscription"]["product_package_id"] == 6){

                $user = User::find($office->user_id);

                switch ($subscription["date_difference"]) {
                    case 5:
                        $notification->name = "You have 5 days remaining of trial.";
                        $notification->link = "https://helpdesk.blackboardbs.com";
                        $notification->save();
                        Mail::to($user->email)->send(new TrialNotificationMail($subscription["date_difference"], $user));
                        break;
                    case 3:
                        $notification->name = "You have 3 days remaining of trial.";
                        $notification->link = "https://helpdesk.blackboardbs.com";
                        $notification->save();
                        Mail::to($user->email)->send(new TrialNotificationMail($subscription["date_difference"], $user));
                        break;
                    case 2:
                        $notification->name = "You have 2 days remaining of trial.";
                        $notification->link = "https://helpdesk.blackboardbs.com";
                        $notification->save();
                        Mail::to($user->email)->send(new TrialNotificationMail($subscription["date_difference"], $user));
                        break;
                    case 1:
                        $notification->name = "You have 1 days remaining of trial.";
                        $notification->link = "https://helpdesk.blackboardbs.com";
                        $notification->save();
                        Mail::to($user->email)->send(new TrialNotificationMail($subscription["date_difference"], $user));
                        break;
                }
            }
        }
    }
}
