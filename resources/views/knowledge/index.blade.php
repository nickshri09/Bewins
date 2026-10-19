<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bewin Services - Financial Solutions</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', sans-serif;
            line-height: 1;
            color: #333;
            overflow-x: hidden;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Modern Header */
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            display: flex;
            align-items: center;
            font-size: 1.5rem;
            font-weight: 700;
            color: #2c3e50;
        }
        .logo-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 12px;
            font-weight: 800;
            box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
        }
        .nav-links {
            display: flex;
            list-style: none;
            gap: 2.5rem;
        }
        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s;
            position: relative;
        }
        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transition: width 0.3s;
        }
        .nav-links a:hover::after {
            width: 100%;
        }
        .nav-links a:hover {
            color: #667eea;
        }
        .login-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        
        /* Modern Hero */
        .hero {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            padding: 140px 0 100px;
            margin-top: 80px;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23667eea" fill-opacity="0.05" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
            z-index: 0;
        }
        .hero-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            position: relative;
            z-index: 1;
        }
        .hero-text h1 {
            font-size: 3.5rem;
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }
        .hero-text p {
            font-size: 1.2rem;
            color: #64748b;
            margin-bottom: 2rem;
        }
        .hero-buttons {
            display: flex;
            gap: 1.5rem;
        }
        .btn {
            padding: 16px 32px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .btn-primary { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary { 
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }
        .btn-secondary:hover {
            background: #667eea;
            color: white;
            transform: translateY(-3px);
        }

        .hero-visual {
            position: relative;
        }
        .hero-shape {
            width: 100%;
            height: 450px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 30px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);
            position: relative;
        }
        .hero-shape::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.8) 0%, rgba(118, 75, 162, 0.8) 100%);
            z-index: 1;
        }
        .hero-shape img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 30px;
            position: relative;
            z-index: 0;
        }
        .happy-clients {
            position: absolute;
            top: 30px;
            right: 30px;
            background: white;
            padding: 20px 25px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            z-index: 2;
        }
        .happy-clients span:first-child {
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
            display: block;
        }
        .happy-clients span:last-child {
            font-size: 0.9rem;
            color: #64748b;
        }

        /* Modern Dynamic Knowledge Sections */
        .knowledge-section {
            padding: 100px 0;
            position: relative;
        }
        .knowledge-section:nth-child(even) {
            background: #f8fafc;
        }
        .section-title {
            text-align: center;
            font-size: 2.8rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 4rem;
            position: relative;
        }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 2px;
        }
        .knowledge-item {
            margin-bottom: 6rem;
            position: relative;
        }
        .knowledge-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }
        .knowledge-text {
            padding: 2rem;
        }
        .knowledge-text h3 {
            font-size: 2.2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 1.5rem;
            position: relative;
        }
        .knowledge-text h3::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 70px;
            height: 3px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 2px;
        }
        .knowledge-text p {
            color: #64748b;
            line-height: 1.7;
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }
        .knowledge-image {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            height: 400px;
            transition: all 0.5s;
        }
        .knowledge-image:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 50px rgba(0,0,0,0.15);
        }
        .knowledge-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.7s;
        }
        .knowledge-image:hover img {
            transform: scale(1.05);
        }
        .knowledge-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            z-index: 1;
        }
        
        /* Service Cards */
        .service-features {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 2rem;
        }
        .service-feature {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 0.95rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        .service-feature:hover {
            background: #667eea;
            color: white;
            transform: translateY(-3px);
        }
        .learn-more-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #667eea;
            font-weight: 600;
            text-decoration: none;
            margin-top: 1.5rem;
            font-size: 1.1rem;
            transition: all 0.3s;
        }
        .learn-more-btn:hover {
            gap: 15px;
            color: #764ba2;
        }

        /* Modern Pricing */
        .pricing {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 100px 0;
            position: relative;
        }
        .pricing::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23667eea" fill-opacity="0.05" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
            z-index: 0;
        }
        .pricing h2 {
            text-align: center;
            font-size: 2.8rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 4rem;
            position: relative;
        }
        .pricing h2::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 2px;
        }
        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2.5rem;
            position: relative;
            z-index: 1;
        }
        .pricing-card {
            background: white;
            padding: 2.5rem;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 15px 30px rgba(0,0,0,0.08);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        .pricing-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }
        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.12);
        }
        .price { 
            font-size: 2.8rem; 
            font-weight: 800; 
            color: #2c3e50; 
            margin-bottom: 0.5rem; 
        }
        .plan-name { 
            color: #64748b; 
            margin-bottom: 2rem; 
            font-size: 1.2rem;
        }
        .validity {
            color: #667eea;
            font-weight: 600;
            margin-bottom: 2rem;
            font-size: 1rem;
        }
        .pricing-features { 
            list-style: none; 
            margin-bottom: 2.5rem; 
        }
        .pricing-features li {
            padding: 1rem 0;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .feature-status {
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .feature-status.yes { 
            background: rgba(102, 126, 234, 0.1); 
            color: #667eea; 
        }
        .feature-status.no { 
            background: rgba(239, 68, 68, 0.1); 
            color: #ef4444; 
        }
        .pricing-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 14px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        .pricing-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(102, 126, 234, 0.4);
        }

        /* Modern Footer */
        .footer {
            /*background: #1e293b;*/
            background: #14348e;
            /*background: #6972D9;*/
            
            padding: 70px 0 30px;
            color: #e2e8f0;
        }
        .footer-content {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 3rem;
            margin-bottom: 3rem;
        }
        .footer-logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: white;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .footer-logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
        }
        .footer-description {
            color: #94a3b8;
            margin-bottom: 1.5rem;
            line-height: 1.7;
        }
        .footer-links h4 {
            color: white;
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
            position: relative;
        }
        .footer-links h4::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 40px;
            height: 3px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 2px;
        }
        .footer-links ul { 
            list-style: none; 
        }
        .footer-links li { 
            margin-bottom: 0.8rem; 
        }
        .footer-links a {
            color: #94a3b8;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-block;
        }
        .footer-links a:hover {
            color: #667eea;
            transform: translateX(5px);
        }
        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        .social-links a {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s;
        }
        .social-links a:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transform: translateY(-5px);
        }
        .footer-bottom {
            border-top: 1px solid #334155;
            padding-top: 2rem;
            text-align: center;
            color: #94a3b8;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .hero-content {
                grid-template-columns: 1fr;
                gap: 3rem;
            }
            .hero-text h1 {
                font-size: 2.8rem;
            }
            .knowledge-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            .pricing-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .hero-text h1 {
                font-size: 2.5rem;
            }
            .pricing-grid {
                grid-template-columns: 1fr;
            }
            .footer-content {
                grid-template-columns: 1fr;
                text-align: center;
            }
            .footer-links h4::after {
                left: 50%;
                transform: translateX(-50%);
            }
            .social-links {
                justify-content: center;
            }
            .nav-links {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="nav">
                <a href="{{ route('dashboard.index') }}" class="flex items-center space-x-2">
                    <div class="logo-icon">B</div>
                    <span>BEWIN SERVICES</span>
                </a>
                <ul class="nav-links">
                    <li><a href="#home">Home</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#footer">Contact</a></li>
                    <li><a href="#about">About</a></li>
                </ul>
                <!--<a href="/admin/knowledge-base" class="login-btn">Login</a>-->
                <a  href="{{ route('admin.knowledge-base.index') }}" class="login-btn">Login </a>
                
            </nav>
        </div>
    </header>

    <!-- Hero -->
    <section class="hero" id="home">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <div class="subtitle" style="color: #667eea; font-weight: 600; margin-bottom: 1rem;">Instant PAN Card</div>
                    <h1>Generate PAN Card Instantly with Bewins!</h1>
                    <p>The Connector Portal gains enhanced capability with instant PAN Card issuance.</p>
                    <div class="hero-buttons">
                        <a href="#" class="btn btn-primary">
                            <i class="fas fa-search"></i> Find a Partner
                        </a>
                        <a href="#" class="btn btn-secondary">
                            <i class="fas fa-rocket"></i> Get Started Now
                        </a>
                    </div>
                </div>
                <div class="hero-visual">
                    <!--<div class="hero-shape">-->
                        <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=500&h=400&fit=crop" alt="Professional Meeting">
                        <div class="happy-clients">
                            <span>25k+</span>
                            <span>Happy Clients</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Enhanced Home Section (Dynamic) -->
    @if(isset($knowledgeByCategory['home']) && $knowledgeByCategory['home']->isNotEmpty())
    <section class="knowledge-section" id="home-section">
        <div class="container">
            <h2 class="section-title">Why Choose Bewin Services</h2>
            @foreach($knowledgeByCategory['home'] as $index => $item)
                <div class="knowledge-item">
                    @if($index % 2 == 0)
                        <!-- Even index: Image on left, content on right -->
                        <div class="knowledge-content">
                            <div class="knowledge-image">
                                @if($item->images->isNotEmpty())
                                    <img src="{{ asset('storage/app/public/' . $item->images->first()->path) }}" 
                                         alt="{{ $item->title }}">
                                @else
                                    <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=600&h=400&fit=crop" 
                                         alt="{{ $item->title }}">
                                @endif
                            </div>
                            <div class="knowledge-text">
                                <h3>{{ $item->title }}</h3>
                                <p>{{ $item->description }}</p>
                                <div class="service-features">
                                    <span class="service-feature"><i class="fas fa-check-circle"></i> Quick Processing</span>
                                    <span class="service-feature"><i class="fas fa-check-circle"></i> Expert Support</span>
                                    <span class="service-feature"><i class="fas fa-check-circle"></i> Secure Transactions</span>
                                </div>
                                <a href="#" class="learn-more-btn">
                                    Learn More <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    @else
                        <!-- Odd index: Image on right, content on left -->
                        <div class="knowledge-content">
                            <div class="knowledge-text">
                                <h3>{{ $item->title }}</h3>
                                <p>{{ $item->description }}</p>
                                <div class="service-features">
                                    <span class="service-feature"><i class="fas fa-check-circle"></i> Quick Processing</span>
                                    <span class="service-feature"><i class="fas fa-check-circle"></i> Expert Support</span>
                                    <span class="service-feature"><i class="fas fa-check-circle"></i> Secure Transactions</span>
                                </div>
                                <a href="#" class="learn-more-btn">
                                    Learn More <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                            <div class="knowledge-image">
                                @if($item->images->isNotEmpty())
                                    <img src="{{ asset('storage/app/public/' . $item->images->first()->path) }}" 
                                         alt="{{ $item->title }}">
                                @else
                                    <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=600&h=400&fit=crop" 
                                         alt="{{ $item->title }}">
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </section>
    @endif





  @if(isset($knowledgeByCategory['about']) && $knowledgeByCategory['about']->isNotEmpty())
    <section class="knowledge-section" id="about">
        <div class="container">
            <h2 class="section-title">About Us</h2>
            @foreach($knowledgeByCategory['about'] as $index => $item)
                <div class="knowledge-item">
                    @if($index % 2 == 0)
                        <!-- Even index: Image on right, content on left -->
                        <div class="knowledge-content">
                            <div class="knowledge-text">
                                <h3>{{ $item->title }}</h3>
                                <p>{{ $item->description }}</p>
                                <div class="service-features">
                                    <span class="service-feature"><i class="fas fa-check-circle"></i> Quick Processing</span>
                                    <span class="service-feature"><i class="fas fa-check-circle"></i> Expert Support</span>
                                    <span class="service-feature"><i class="fas fa-check-circle"></i> Secure Transactions</span>
                                </div>
                                <a href="#" class="learn-more-btn">
                                    Learn More <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                            <div class="knowledge-image">
                                @if($item->images->isNotEmpty())
                                    <img src="{{ asset('storage/app/public/' . $item->images->first()->path) }}" 
                                         alt="{{ $item->title }}">
                                @else
                                    <img src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=600&h=400&fit=crop" 
                                         alt="{{ $item->title }}">
                                @endif
                            </div>
                        </div>
                    @else
                        <!-- Odd index: Image on left, content on right -->
                        <div class="knowledge-content">
                            <div class="knowledge-image">
                                @if($item->images->isNotEmpty())
                                    <img src="{{ asset('storage/app/public/' . $item->images->first()->path) }}" 
                                         alt="{{ $item->title }}">
                                @else
                                    <img src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=600&h=400&fit=crop" 
                                         alt="{{ $item->title }}">
                                @endif
                            </div>
                            <div class="knowledge-text">
                                <h3>{{ $item->title }}</h3>
                                <p>{{ $item->description }}</p>
                                <div class="service-features">
                                    <span class="service-feature"><i class="fas fa-check-circle"></i> Quick Processing</span>
                                    <span class="service-feature"><i class="fas fa-check-circle"></i> Expert Support</span>
                                    <span class="service-feature"><i class="fas fa-check-circle"></i> Secure Transactions</span>
                                </div>
                                <a href="#" class="learn-more-btn">
                                    Learn More <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </section>
    @endif
    <!-- Enhanced Services Section (Dynamic) -->
    @if(isset($knowledgeByCategory['service']) && $knowledgeByCategory['service']->isNotEmpty())
    <section class="knowledge-section" id="services">
        <div class="container">
            <h2 class="section-title">Our Services</h2>
            @foreach($knowledgeByCategory['service'] as $index => $item)
                <div class="knowledge-item">
                    @if($index % 2 == 0)
                        <!-- Even index: Image on left, content on right -->
                        <div class="knowledge-content">
                            <div class="knowledge-image">
                                @if($item->images->isNotEmpty())
                                    <img src="{{ asset('storage/app/public/' . $item->images->first()->path) }}" 
                                         alt="{{ $item->title }}">
                                @else
                                    <img src="https://images.unsplash.com/photo-1554224154-260325c05993?w=600&h=400&fit=crop" 
                                         alt="{{ $item->title }}">
                                @endif
                            </div>
                            <div class="knowledge-text">
                                <h3>{{ $item->title }}</h3>
                                <p>{{ $item->description }}</p>
                                <div class="service-features">
                                    <span class="service-feature"><i class="fas fa-check-circle"></i> Quick Processing</span>
                                    <span class="service-feature"><i class="fas fa-check-circle"></i> Expert Support</span>
                                    <span class="service-feature"><i class="fas fa-check-circle"></i> Secure Transactions</span>
                                </div>
                                <a href="#" class="learn-more-btn">
                                    Learn More <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    @else
                        <!-- Odd index: Image on right, content on left -->
                        <div class="knowledge-content">
                            <div class="knowledge-text">
                                <h3>{{ $item->title }}</h3>
                                <p>{{ $item->description }}</p>
                                <div class="service-features">
                                    <span class="service-feature"><i class="fas fa-check-circle"></i> Quick Processing</span>
                                    <span class="service-feature"><i class="fas fa-check-circle"></i> Expert Support</span>
                                    <span class="service-feature"><i class="fas fa-check-circle"></i> Secure Transactions</span>
                                </div>
                                <a href="#" class="learn-more-btn">
                                    Learn More <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                            <div class="knowledge-image">
                                @if($item->images->isNotEmpty())
                                    <img src="{{ asset('storage/app/public/' . $item->images->first()->path) }}" 
                                         alt="{{ $item->title }}">
                                @else
                                    <img src="https://images.unsplash.com/photo-1554224154-260325c05993?w=600&h=400&fit=crop" 
                                         alt="{{ $item->title }}">
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Enhanced About Section (Dynamic) -->
  

    <!-- Modern Pricing -->
    <section class="pricing">
        <div class="container">
            <h2>Pricing Plans</h2>
            <div class="pricing-grid">
                @forelse($pricingPlans as $plan)
                    <div class="pricing-card">
                        <div class="price">₹{{ number_format($plan->price) }}/-</div>
                        <div class="plan-name">{{ $plan->name }}</div>
                        <div class="validity">Validity {{ $plan->validity }}</div>
                        <ul class="pricing-features">
                            @foreach($plan->features as $feature)
                                <li>{{ $feature->feature_name }}
                                    <span class="feature-status {{ $feature->feature_status }}">
                                        {{ ucfirst($feature->feature_status) }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                        <a href="{{ route('knowledge.plan.show', $plan->id) }}" class="pricing-btn">Subscribe Now →</a>
                    </div>
                @empty
                    <p>No plans available.</p>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Modern Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-logo">
                        <div class="footer-logo-icon">B</div>
                        <span>BEWIN SERVICES</span>
                    </div>
                    <p class="footer-description">Your trusted partner for all financial and business services. We provide comprehensive solutions tailored to your needs.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="footer-links">
                    <h4>Company Info</h4>
                    <ul>
                        <li><a href="#">About</a></li>
                        <li><a href="#">Career</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Our Locations</a></li>
                        <li><a href="#">Terms</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms & Conditions</a></li>
                        <li><a href="#">Refund Policy</a></li>
                        <li><a href="#">Disclaimer</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Contact Info</h4>
                    <ul>
                        <li>Textile Colony,Dumraon, 
                        Dist.Buxar-802119</li>
                        <li>bewinseervicespvtltd@gmail.com</li>
                        <li>+91 80929 74141</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Bewin Services. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>