<!--**********************************
    Footer start
***********************************-->
<div class="footer">
    <div class="copyright">
        <p class="mb-0"><strong>Harris n Pop Engineer Request</strong> © {{ date('Y') }} All Rights Reserved</p>
        <p class="mb-0 fs-12">Made with <span class="heart"></span> by IT Harris</p>
        @if (auth()->user()->role === 'superadmin')
            <p class="mt-1 mb-0 text-muted" style="font-size: 10px;">
                <i class="fa fa-server"></i> v1.0.0 |
                <i class="fa fa-users"></i> {{ \App\Models\User::count() }} users |
                <i class="fa fa-ticket"></i> {{ \App\Models\Ticket::count() }} tickets
            </p>
        @endif
    </div>
</div>
<!--**********************************
    Footer end
***********************************-->
