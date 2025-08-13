@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
    /* إزالة فورية لأي خط أبيض */
    
    /* Modern Professional Design */
    :root {
        --primary-color: #4f46e5;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --info-color: #06b6d4;
        --dark-color: #1e293b;
        --border-radius: 12px;
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    }

    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        min-height: 100vh;
        font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        position: relative;
        overflow-x: hidden;
    }

    /* إزالة الخط الأبيض من السايدبار */
    .pcoded-navbar,
    .pcoded-inner-navbar,
    .pcoded-item,
    .pcoded-navigatio-lavel {
        border: none !important;
    }

    .pcoded-item li,
    .pcoded-item li a {
        border: none !important;
        border-bottom: none !important;
    }

    /* إزالة الخط الأبيض فوق السايدبار بقوة */
    .pcoded-main-container,
    .pcoded-wrapper,
    .pcoded-navbar,
    .pcoded-container,
    .pcoded,
    #pcoded {
        border-top: none !important;
        border: none !important;
        border-bottom: none !important;
        border-left: none !important;
        border-right: none !important;
        outline: none !important;
        box-shadow: none !important;
    }

    /* إزالة جميع العناصر الوهمية */
    .pcoded-main-container::before,
    .pcoded-main-container::after,
    .pcoded-wrapper::before,
    .pcoded-wrapper::after,
    .pcoded-navbar::before,
    .pcoded-navbar::after,
    .pcoded::before,
    .pcoded::after,
    #pcoded::before,
    #pcoded::after {
        display: none !important;
        content: none !important;
    }

    /* إزالة أي خط من navbar */
    nav.pcoded-navbar,
    .navbar.pcoded-header,
    .header-navbar {
        border: none !important;
        border-top: none !important;
        border-bottom: none !important;
    }



    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="90" cy="50" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        pointer-events: none;
        z-index: -1;
    }

    .page-header {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.2);
        margin-bottom: 2rem;
        padding: 3rem 2rem;
        border: none;
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #667eea);
        background-size: 300% 100%;
        animation: shimmer 3s ease-in-out infinite;
        border-radius: 20px 20px 0 0;
    }

    @keyframes shimmer {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    .page-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--dark-color);
        margin: 0;
        text-align: center;
        background: linear-gradient(135deg, var(--primary-color), #9333ea);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .page-subtitle {
        font-size: 1.1rem;
        color: #64748b;
        text-align: center;
        margin: 0.5rem 0 0 0;
    }

    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.3);
        transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--primary-color), #9333ea);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.4s ease;
        border-radius: 20px 20px 0 0;
    }

    .stat-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0));
        opacity: 0;
        transition: opacity 0.4s ease;
        pointer-events: none;
    }

    .stat-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
    }

    .stat-card:hover::before {
        transform: scaleX(1);
    }

    .stat-card:hover::after {
        opacity: 1;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        color: white;
        margin-bottom: 1.5rem;
        position: relative;
        transition: all 0.4s ease;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .stat-icon::before {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        border-radius: 18px;
        background: linear-gradient(45deg, rgba(255, 255, 255, 0.3), transparent);
        z-index: -1;
        opacity: 0;
        transition: opacity 0.4s ease;
    }

    .stat-card:hover .stat-icon {
        transform: rotateY(360deg) scale(1.1);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
    }

    .stat-card:hover .stat-icon::before {
        opacity: 1;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--dark-color);
        margin: 0;
        line-height: 1;
        position: relative;
        transition: all 0.4s ease;
    }

    .stat-card:hover .stat-number {
        transform: scale(1.1);
        color: var(--primary-color);
    }

    .stat-label {
        font-size: 0.95rem;
        color: #64748b;
        margin: 0;
        font-weight: 500;
        letter-spacing: 0.5px;
        transition: color 0.4s ease;
    }

    .stat-card:hover .stat-label {
        color: var(--dark-color);
    }

    .actions-bar {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.3);
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        justify-content: space-between;
        align-items: center;
        position: relative;
        overflow: hidden;
    }

    .actions-bar::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.3), transparent);
        animation: slide 4s linear infinite;
        border-radius: 20px 20px 0 0;
    }

    @keyframes slide {
        0% { left: -100%; }
        100% { left: 100%; }
    }

    .search-container {
        flex: 1;
        min-width: 300px;
        position: relative;
    }

    .search-input {
        width: 100%;
        padding: 1rem 1.25rem 1rem 3rem;
        border: 2px solid rgba(226, 232, 240, 0.5);
        border-radius: 16px;
        font-size: 1rem;
        transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        font-family: 'Cairo', sans-serif;
        color: var(--dark-color);
    }

    .search-input::placeholder {
        color: #94a3b8;
        font-weight: 400;
    }

    .search-input:focus {
        outline: none;
        border-color: var(--primary-color);
        background: rgba(255, 255, 255, 1);
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1), 0 10px 30px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .search-icon {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 1.1rem;
        transition: all 0.4s ease;
    }

    .search-input:focus + .search-icon {
        color: var(--primary-color);
        transform: translateY(-50%) scale(1.1);
    }

    .btn-modern {
        padding: 1rem 2rem;
        border-radius: 16px;
        font-weight: 600;
        font-size: 0.95rem;
        border: none;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        text-decoration: none;
        position: relative;
        overflow: hidden;
        font-family: 'Cairo', sans-serif;
        letter-spacing: 0.5px;
    }

    .btn-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }

    .btn-modern:hover::before {
        left: 100%;
    }

    .btn-primary-modern {
        background: linear-gradient(135deg, var(--primary-color), #9333ea);
        color: white;
        box-shadow: 0 8px 25px rgba(79, 70, 229, 0.3);
    }

    .btn-primary-modern:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 15px 35px rgba(79, 70, 229, 0.4);
        color: white;
    }

    .btn-info-modern {
        background: linear-gradient(135deg, var(--info-color), #0891b2);
        color: white;
        box-shadow: 0 8px 25px rgba(6, 182, 212, 0.3);
    }

    .btn-info-modern:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 15px 35px rgba(6, 182, 212, 0.4);
        color: white;
    }

    .btn-success-modern {
        background: linear-gradient(135deg, var(--success-color), #059669);
        color: white;
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
    }

    .btn-success-modern:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 15px 35px rgba(16, 185, 129, 0.4);
        color: white;
    }

    .btn-warning-modern {
        background: linear-gradient(135deg, var(--warning-color), #d97706);
        color: white;
        box-shadow: 0 8px 25px rgba(245, 158, 11, 0.3);
    }

    .btn-warning-modern:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 15px 35px rgba(245, 158, 11, 0.4);
        color: white;
    }

    .btn-danger-modern {
        background: linear-gradient(135deg, var(--danger-color), #dc2626);
        color: white;
        box-shadow: 0 8px 25px rgba(239, 68, 68, 0.3);
    }

    .btn-danger-modern:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 15px 35px rgba(239, 68, 68, 0.4);
        color: white;
    }

    .requests-container {
        display: grid;
        gap: 1.5rem;
    }

    .request-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.3);
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        position: relative;
    }

    .request-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.03), rgba(147, 51, 234, 0.03));
        opacity: 0;
        transition: opacity 0.4s ease;
        pointer-events: none;
    }

    .request-card:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
    }

    .request-card:hover::before {
        opacity: 1;
    }

    .request-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
    }

    .volunteer-info h3 {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--dark-color);
        margin: 0 0 0.5rem 0;
    }

    .volunteer-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        font-size: 0.875rem;
        color: #64748b;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-pending {
        background: rgba(245, 158, 11, 0.1);
        color: #d97706;
        border: 1px solid rgba(245, 158, 11, 0.2);
    }

    .status-approved {
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .status-rejected {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.2);
    }

    .request-body {
        padding: 1.5rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .info-item {
        padding: 1rem;
        background: #f8fafc;
        border-radius: 8px;
        border-right: 4px solid var(--primary-color);
    }

    .info-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }

    .info-value {
        font-size: 0.875rem;
        color: var(--dark-color);
        font-weight: 500;
    }

    .evaluation-score {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .score-excellent {
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
    }

    .score-good {
        background: rgba(245, 158, 11, 0.1);
        color: #d97706;
    }

    .score-poor {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
    }

    .actions-bar-card {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        justify-content: flex-end;
        padding-top: 1rem;
        border-top: 1px solid #e2e8f0;
    }

    .btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-md);
        border: none;
    }

    .empty-icon {
        font-size: 4rem;
        color: #cbd5e1;
        margin-bottom: 1rem;
    }

    .empty-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 0.5rem;
    }

    .empty-text {
        color: #64748b;
        margin-bottom: 2rem;
    }

    @media (max-width: 768px) {
        .page-title {
            font-size: 2rem;
        }
        
        .actions-bar {
            flex-direction: column;
            align-items: stretch;
        }
        
        .search-container {
            min-width: unset;
        }
        
        .info-grid {
            grid-template-columns: 1fr;
        }
        
        .request-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
    </style>

<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">إدارة طلبات التطوع</h1>
        <p class="page-subtitle">تتبع وإدارة جميع طلبات التطوع بسهولة ومرونة</p>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-container">
        @php
            $totalRequests = $requests->count();
            $pendingRequests = $requests->where('status', 'pending')->count();
            $approvedRequests = $requests->where('status', 'approved')->count();
            $rejectedRequests = $requests->where('status', 'rejected')->count();
        @endphp
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--primary-color), #9333ea);">
                <i class="fas fa-users"></i>
            </div>
            <h3 class="stat-number">{{ $totalRequests }}</h3>
            <p class="stat-label">إجمالي الطلبات</p>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--warning-color), #d97706);">
                <i class="fas fa-clock"></i>
            </div>
            <h3 class="stat-number">{{ $pendingRequests }}</h3>
            <p class="stat-label">في الانتظار</p>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--success-color), #059669);">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3 class="stat-number">{{ $approvedRequests }}</h3>
            <p class="stat-label">مقبولة</p>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--danger-color), #dc2626);">
                <i class="fas fa-times-circle"></i>
            </div>
            <h3 class="stat-number">{{ $rejectedRequests }}</h3>
            <p class="stat-label">مرفوضة</p>
        </div>
    </div>

    <!-- Actions Bar -->
    <div class="actions-bar">
        <div class="search-container">
            <input type="text" class="search-input" placeholder="البحث في الطلبات..." id="searchInput">
            <i class="fas fa-search search-icon"></i>
                    </div>
        
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <a href="{{ route('volunteer-requests.create') }}" class="btn-modern btn-primary-modern">
                <i class="fas fa-plus"></i> طلب جديد
            </a>
            <a href="{{ route('volunteer-evaluations.index') }}" class="btn-modern btn-info-modern">
                            <i class="fas fa-chart-bar"></i> التقييمات
                        </a>
            <a href="{{ route('approval-decisions.statistics') }}" class="btn-modern btn-success-modern">
                <i class="fas fa-chart-pie"></i> الإحصائيات
                        </a>
                    </div>
                </div>

    <!-- Success/Error Messages -->
                    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #059669; border-radius: var(--border-radius);">
            <i class="fas fa-check-circle"></i>
                            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #dc2626; border-radius: var(--border-radius);">
            <i class="fas fa-exclamation-triangle"></i>
                            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

    <!-- Requests Container -->
    <div class="requests-container" id="requestsContainer">
        @forelse($requests as $request)
            <div class="request-card" data-search-content="{{ strtolower($request->full_name . ' ' . $request->email . ' ' . $request->phone . ' ' . $request->preferred_area) }}">
                <div class="request-header">
                    <div class="volunteer-info">
                        <h3>{{ $request->full_name }}</h3>
                        <div class="volunteer-meta">
                            <span class="meta-item">
                                <i class="fas fa-envelope"></i>
                                {{ $request->email }}
                            </span>
                            <span class="meta-item">
                                <i class="fas fa-phone"></i>
                                {{ $request->phone }}
                            </span>
                            <span class="meta-item">
                                <i class="fas fa-calendar"></i>
                                {{ $request->created_at->format('Y-m-d') }}
                            </span>
                        </div>
                    </div>
                    
                    <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 1rem;">
                        @php
                            $statusClass = match($request->status) {
                                'approved' => 'status-approved',
                                'rejected' => 'status-rejected',
                                default => 'status-pending'
                                            };
                                            $statusText = match($request->status) {
                                'approved' => 'مقبول',
                                                'rejected' => 'مرفوض',
                                default => 'قيد المراجعة'
                                            };
                                        @endphp
                        <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                        
                                        @if($request->hasEvaluation())
                                            @php
                                                $evaluation = $request->latestEvaluation;
                                $scoreClass = $evaluation->overall_score >= 37 ? 'score-excellent' : ($evaluation->overall_score >= 25 ? 'score-good' : 'score-poor');
                                            @endphp
                            <div class="evaluation-score {{ $scoreClass }}">
                                                    <i class="fas fa-star"></i>
                                {{ $evaluation->overall_score }}/50
                                                </div>
                                            @endif
                    </div>
                    </div>

                <div class="request-body">
                    <div class="info-grid">
                        @if($request->education_level)
                            <div class="info-item">
                                <div class="info-label">المستوى التعليمي</div>
                                <div class="info-value">{{ $request->education_level }}</div>
                                </div>
                        @endif
                        
                        @if($request->field_of_study)
                            <div class="info-item">
                                <div class="info-label">مجال الدراسة</div>
                                <div class="info-value">{{ $request->field_of_study }}</div>
                            </div>
                        @endif
                        
                        @if($request->preferred_area)
                            <div class="info-item">
                                <div class="info-label">المجال المفضل</div>
                                <div class="info-value">{{ $request->preferred_area }}</div>
                            </div>
                        @endif
                        
                        @if($request->availability)
                            <div class="info-item">
                                <div class="info-label">التوفر</div>
                                <div class="info-value">{{ $request->availability }}</div>
                            </div>
                        @endif
                        
                        @if($request->gender)
                            <div class="info-item">
                                <div class="info-label">الجنس</div>
                                <div class="info-value">{{ $request->gender === 'male' ? 'ذكر' : 'أنثى' }}</div>
                            </div>
                        @endif
                        
                        @if($request->age)
                            <div class="info-item">
                                <div class="info-label">العمر</div>
                                <div class="info-value">{{ $request->age }} سنة</div>
                </div>
                        @endif
                    </div>
                    
                    <div class="actions-bar-card">
                        <a href="{{ route('volunteer-requests.show', $request->id) }}" class="btn-modern btn-info-modern btn-sm">
                            <i class="fas fa-eye"></i> عرض
                        </a>
                        
                        @if(!$request->hasEvaluation())
                            <a href="{{ route('volunteer-evaluations.create', $request->id) }}" class="btn-modern btn-primary-modern btn-sm">
                                <i class="fas fa-clipboard-check"></i> تقييم
                            </a>
                        @else
                            <a href="{{ route('volunteer-evaluations.show', $request->latestEvaluation->id) }}" class="btn-modern btn-success-modern btn-sm">
                                <i class="fas fa-clipboard-check"></i> التقييم
                            </a>
                        @endif
                        
                        <a href="{{ route('volunteer-requests.edit', $request->id) }}" class="btn-modern btn-warning-modern btn-sm">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                        
                        <form method="POST" action="{{ route('volunteer-requests.destroy', $request->id) }}" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا الطلب؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-modern btn-danger-modern btn-sm">
                                <i class="fas fa-trash"></i> حذف
                            </button>
                        </form>
                        </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h2 class="empty-title">لا توجد طلبات تطوع</h2>
                <p class="empty-text">لم يتم تقديم أي طلبات تطوع بعد. ابدأ بإضافة طلب جديد.</p>
                <a href="{{ route('volunteer-requests.create') }}" class="btn-modern btn-primary-modern">
                    <i class="fas fa-plus"></i> إضافة طلب تطوع جديد
                </a>
            </div>
        @endforelse
    </div>
    </div>

    <script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const cards = document.querySelectorAll('.request-card');
        
        cards.forEach(card => {
            const searchContent = card.getAttribute('data-search-content');
            if (searchContent.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
        
        // Show/hide empty state for search
        const visibleCards = document.querySelectorAll('.request-card[style="display: block"], .request-card:not([style*="display: none"])');
        
        if (visibleCards.length === 0 && searchTerm !== '') {
            if (!document.getElementById('search-empty-state')) {
                const searchEmptyState = document.createElement('div');
                searchEmptyState.id = 'search-empty-state';
                searchEmptyState.className = 'empty-state';
                searchEmptyState.innerHTML = `
                    <div class="empty-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h2 class="empty-title">لا توجد نتائج</h2>
                    <p class="empty-text">لم يتم العثور على طلبات تطوع تطابق كلمة البحث "${searchTerm}"</p>
                `;
                document.getElementById('requestsContainer').appendChild(searchEmptyState);
            }
            } else {
            const searchEmptyState = document.getElementById('search-empty-state');
            if (searchEmptyState) {
                searchEmptyState.remove();
            }
        }
    });

    // Smooth animations on page load with improved effects
    document.addEventListener('DOMContentLoaded', function() {
        // Page loading animation
        document.body.style.opacity = '0';
        setTimeout(() => {
            document.body.style.transition = 'opacity 0.5s ease';
            document.body.style.opacity = '1';
        }, 100);

        // Animate page header
        const pageHeader = document.querySelector('.page-header');
        if (pageHeader) {
            pageHeader.style.opacity = '0';
            pageHeader.style.transform = 'translateY(-30px) scale(0.95)';
            setTimeout(() => {
                pageHeader.style.transition = 'all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                pageHeader.style.opacity = '1';
                pageHeader.style.transform = 'translateY(0) scale(1)';
            }, 200);
        }

        // Animate stats cards with stagger effect
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(-30px) rotateX(-10deg)';
            setTimeout(() => {
                card.style.transition = 'all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1)';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0) rotateX(0deg)';
            }, 400 + (index * 100));
        });

        // Animate actions bar
        const actionsBar = document.querySelector('.actions-bar');
        if (actionsBar) {
            actionsBar.style.opacity = '0';
            actionsBar.style.transform = 'translateX(-20px)';
                    setTimeout(() => {
                actionsBar.style.transition = 'all 0.6s ease';
                actionsBar.style.opacity = '1';
                actionsBar.style.transform = 'translateX(0)';
            }, 800);
        }

        // Animate request cards with more dynamic effect
        const cards = document.querySelectorAll('.request-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(40px) scale(0.9) rotateX(10deg)';
            setTimeout(() => {
                card.style.transition = 'all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0) scale(1) rotateX(0deg)';
            }, 1000 + (index * 100));
        });

        // Number counting animation for stats
        const statNumbers = document.querySelectorAll('.stat-number');
        statNumbers.forEach((numberEl, index) => {
            const finalNumber = parseInt(numberEl.textContent);
            let currentNumber = 0;
            const increment = finalNumber / 30;
            
            setTimeout(() => {
                const timer = setInterval(() => {
                    currentNumber += increment;
                    if (currentNumber >= finalNumber) {
                        currentNumber = finalNumber;
                        clearInterval(timer);
                    }
                    numberEl.textContent = Math.floor(currentNumber);
                }, 50);
            }, 600 + (index * 100));
        });

        // Add floating animation to stat icons
        const statIcons = document.querySelectorAll('.stat-icon');
        statIcons.forEach((icon, index) => {
            setTimeout(() => {
                icon.style.animation = `float 3s ease-in-out infinite ${index * 0.5}s`;
            }, 1000);
            });
        });
    </script>

<style>
    /* Floating animation for stat icons */
    @keyframes float {
        0%, 100% { transform: translateY(0px) rotateY(0deg); }
        50% { transform: translateY(-10px) rotateY(180deg); }
    }
</style>
@endsection
