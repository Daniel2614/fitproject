<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\VitalGym\Entities\Membership;
use App\Notifications\MembershipPaymentReminder;

class SendMembershipPaymentReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:membership-payment-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification to customer 5 days before your membership expires';

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
        $memberships = Membership::with('customer')->whereDate('date_end', today()->addDays(5)->toDateString())->get();

        if ($memberships->count() === 0) {
            return $this->info('There are not customers to notify');
        }

        $memberships->unique('customer')->each(function ($membership) {
            $membership->customer->notify(new MembershipPaymentReminder($membership));
        });

        return $this->info('The notification has been sent');
    }
}
