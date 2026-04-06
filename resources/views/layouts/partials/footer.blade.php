<!--**********************************
    Footer start
***********************************-->
<div class="footer">
    <div class="copyright">
        <p class="mb-0">© {{ date('Y') }} Harris Hotel & Pop Hotel Ticketing System</p>
        <p class="mb-0">Made with <i class="fa fa-heart text-danger"></i> by VHP Team</p>
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
