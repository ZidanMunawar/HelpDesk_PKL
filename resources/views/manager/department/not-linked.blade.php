@extends('layouts.main')

@section('title', 'My Department | ' . config('app.name'))

@section('page-title', 'My Department')

@section('content')
    <div style="min-height: 70vh; display: flex; align-items: center; justify-content: center; padding: 20px;">
        <div
            style="background: white; border-radius: 12px; padding: 40px; text-align: center; max-width: 450px; box-shadow: 0 10px 30px rgba(0,51,102,0.1);">
            <div
                style="width: 80px; height: 80px; background: #fff3e0; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; font-size: 40px; color: #ff6600;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h2 style="font-size: 24px; font-weight: 700; color: #003366; margin-bottom: 15px;">No Department Linked</h2>
            <p style="color: #666; margin-bottom: 25px; line-height: 1.6;">
                Your account is not currently linked to any department as a manager.<br>
                Please contact the administrator to assign you to a department.
            </p>
            <a href="mailto:{{ config('mail.admin_email', 'admin@hotel.com') }}"
                style="background: #003366; color: white; padding: 12px 30px; border-radius: 6px; text-decoration: none; display: inline-block;">
                <i class="fas fa-envelope me-2"></i> Contact Administrator
            </a>
            <div style="margin-top: 25px; padding-top: 25px; border-top: 1px solid #f0f0f0; font-size: 13px; color: #888;">
                <i class="fas fa-info-circle" style="color: #ff6600;"></i>
                You need to be assigned as manager of a department to access this page.
            </div>
        </div>
    </div>
@endsection
