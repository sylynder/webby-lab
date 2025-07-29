<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechGenius Internships - Full Stack & AI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #f59e0b;
            --dark: #1e293b;
            --light: #f8fafc;
            --gray: #94a3b8;
            --success: #10b981;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--light);
            color: var(--dark);
            line-height: 1.6;
        }

        header {
            background-color: var(--dark);
            color: white;
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo i {
            color: var(--secondary);
            font-size: 1.5rem;
        }

        .logo h1 {
            font-weight: 700;
            font-size: 1.5rem;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-links a:hover {
            color: var(--secondary);
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--secondary);
            transition: width 0.3s ease;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .mobile-menu {
            display: none;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .hero {
            background: linear-gradient(rgba(30, 41, 59, 0.9), rgba(30, 41, 59, 0.8)), url('https://images.unsplash.com/photo-1620712943543-bcc4688e7485?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 5rem 0;
            text-align: center;
        }

        .hero h2 {
            font-size: 3rem;
            margin-bottom: 1rem;
            background: linear-gradient(to right, white, var(--gray));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 2rem;
            color: var(--gray);
        }

        .btn {
            display: inline-block;
            background-color: var(--primary);
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid var(--primary);
        }

        .btn:hover {
            background-color: transparent;
            color: var(--primary);
        }

        .btn-secondary {
            background-color: transparent;
            color: var(--secondary);
            border-color: var(--secondary);
            margin-left: 1rem;
        }

        .btn-secondary:hover {
            background-color: var(--secondary);
            color: var(--dark);
        }

        section {
            padding: 4rem 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-title h2 {
            font-size: 2.5rem;
            color: var(--dark);
            margin-bottom: 1rem;
            position: relative;
            display: inline-block;
        }

        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            border-radius: 2px;
        }

        .programs {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
        }

        .program-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .program-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .program-img {
            height: 200px;
            overflow: hidden;
        }

        .program-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .program-card:hover .program-img img {
            transform: scale(1.1);
        }

        .program-content {
            padding: 1.5rem;
        }

        .program-content h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }

        .program-content .badge {
            display: inline-block;
            background-color: var(--primary);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 50px;
            font-size: 0.8rem;
            margin-bottom: 1rem;
        }

        .program-content p {
            color: var(--gray);
            margin-bottom: 1rem;
        }

        .program-content ul {
            margin-bottom: 1.5rem;
            padding-left: 1.5rem;
        }

        .program-content ul li {
            margin-bottom: 0.5rem;
        }

        .features {
            background-color: var(--dark);
            color: white;
        }

        .features .section-title h2 {
            color: white;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 2rem;
            border-radius: 10px;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            background-color: rgba(255, 255, 255, 0.2);
        }

        .feature-card i {
            font-size: 2.5rem;
            color: var(--secondary);
            margin-bottom: 1rem;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .feature-card p {
            color: var(--gray);
        }

        .testimonials .testimonial-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .testimonial-card {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .testimonial-card::before {
            content: '\201C';
            font-size: 5rem;
            color: rgba(37, 99, 235, 0.1);
            position: absolute;
            top: 1rem;
            left: 1rem;
            line-height: 1;
        }

        .testimonial-card .rating {
            color: var(--secondary);
            margin-bottom: 1rem;
        }

        .testimonial-card p {
            font-style: italic;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .testimonial-card .author {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .testimonial-card .author img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .author-info h4 {
            font-size: 1.1rem;
        }

        .author-info p {
            font-style: normal;
            color: var(--gray);
            font-size: 0.9rem;
        }

        .stats {
            background-color: var(--primary);
            color: white;
            text-align: center;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
        }

        .stat-item h3 {
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }

        .stat-item p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.1rem;
        }

        .application-form {
            background-color: white;
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid var(--gray);
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }

        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .submit-btn:hover {
            background-color: var(--primary-dark);
        }

        footer {
            background-color: var(--dark);
            color: white;
            padding: 3rem 0 1rem;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-col h3 {
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            color: var(--secondary);
        }

        .footer-col p {
            color: var(--gray);
            margin-bottom: 1rem;
        }

        .social-links {
            display: flex;
            gap: 1rem;
        }

        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            color: white;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            background-color: var(--secondary);
            color: var(--dark);
            transform: translateY(-3px);
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.8rem;
        }

        .footer-links a {
            color: var(--gray);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--secondary);
        }

        .copyright {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--gray);
            font-size: 0.9rem;
        }

        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            max-width: 500px;
            width: 90%;
            position: relative;
            transform: translateY(-50px);
            transition: transform 0.3s ease;
        }

        .modal.active .modal-content {
            transform: translateY(0);
        }

        .close-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--gray);
            transition: color 0.3s ease;
        }

        .close-btn:hover {
            color: var(--dark);
        }

        .success-message {
            text-align: center;
        }

        .success-message i {
            font-size: 4rem;
            color: var(--success);
            margin-bottom: 1rem;
        }

        .success-message h3 {
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }

        .success-message p {
            margin-bottom: 2rem;
            color: var(--gray);
        }

        @media (max-width: 768px) {
            .nav-links {
                position: fixed;
                top: 70px;
                left: -100%;
                width: 100%;
                height: calc(100vh - 70px);
                background-color: var(--dark);
                flex-direction: column;
                align-items: center;
                padding-top: 2rem;
                transition: all 0.3s ease;
            }

            .nav-links.active {
                left: 0;
            }

            .mobile-menu {
                display: block;
            }

            .hero h2 {
                font-size: 2.5rem;
            }

            .btn {
                display: block;
                width: 100%;
                margin-bottom: 1rem;
            }

            .btn-secondary {
                margin-left: 0;
            }

            .section-title h2 {
                font-size: 2rem;
            }

            .programs {
                grid-template-columns: 1fr;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }

        /* Animation classes */
        .fade-in {
            animation: fadeIn 0.5s ease forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .delay-1 {
            animation-delay: 0.2s;
        }

        .delay-2 {
            animation-delay: 0.4s;
        }

        .delay-3 {
            animation-delay: 0.6s;
        }

        .delay-4 {
            animation-delay: 0.8s;
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <div class="logo">
                    <i class="fas fa-robot"></i>
                    <h1>TechGenius</h1>
                </div>
                <div class="nav-links">
                    <a href="#programs">Programs</a>
                    <a href="#benefits">Benefits</a>
                    <a href="#testimonials">Testimonials</a>
                    <a href="#apply">Apply Now</a>
                    <a href="#faq">FAQ</a>
                </div>
                <div class="mobile-menu">
                    <i class="fas fa-bars"></i>
                </div>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="container">
            <h2 class="fade-in">Engineering Internships in Full Stack & Generative AI</h2>
            <p class="fade-in delay-1">Join our intensive 6-month internship program and gain hands-on experience with cutting-edge technologies mentored by industry experts.</p>
            <a href="#apply" class="btn fade-in delay-2 pulse">Apply Now</a>
            <a href="#programs" class="btn btn-secondary fade-in delay-3">Learn More</a>
        </div>
    </section>

    <section id="programs" class="programs-section">
        <div class="container">
            <div class="section-title">
                <h2>Our Internship Programs</h2>
                <p>Choose the path that aligns with your career aspirations</p>
            </div>
            <div class="programs">
                <div class="program-card fade-in">
                    <div class="program-img">
                        <img src="https://images.unsplash.com/photo-1623479322729-28b25c16b011?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Full Stack Development">
                    </div>
                    <div class="program-content">
                        <h3>Full Stack Development</h3>
                        <span class="badge">6 Months</span>
                        <p>Master both frontend and backend technologies to build complete web applications from scratch.</p>
                        <ul>
                            <li><i class="fas fa-check-circle"></i> React/Next.js frontend development</li>
                            <li><i class="fas fa-check-circle"></i> Node.js/Express backend APIs</li>
                            <li><i class="fas fa-check-circle"></i> Database design with MongoDB/PostgreSQL</li>
                            <li><i class="fas fa-check-circle"></i> DevOps & deployment (Docker, AWS)</li>
                            <li><i class="fas fa-check-circle"></i> Agile development methodologies</li>
                        </ul>
                        <a href="#apply" class="btn">Apply Now</a>
                    </div>
                </div>
                <div class="program-card fade-in delay-1">
                    <div class="program-img">
                        <img src="https://images.unsplash.com/photo-1643013835312-e897fd81a6df?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Generative AI">
                    </div>
                    <div class="program-content">
                        <h3>Generative AI Engineering</h3>
                        <span class="badge">6 Months</span>
                        <p>Explore the frontier of artificial intelligence with practical applications of generative models.</p>
                        <ul>
                            <li><i class="fas fa-check-circle"></i> Deep learning with PyTorch/TensorFlow</li>
                            <li><i class="fas fa-check-circle"></i> LLM fine-tuning & deployment</li>
                            <li><i class="fas fa-check-circle"></i> Diffusion models for image generation</li>
                            <li><i class="fas fa-check-circle"></i> AI ethics & responsible development</li>
                            <li><i class="fas fa-check-circle"></i> MLOps for scalable AI applications</li>
                        </ul>
                        <a href="#apply" class="btn">Apply Now</a>
                    </div>
                </div>
                <div class="program-card fade-in delay-2">
                    <div class="program-img">
                        <img src="https://images.unsplash.com/photo-1642104704074-907c0698cbd9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Hybrid Program">
                    </div>
                    <div class="program-content">
                        <h3>Full Stack + AI Hybrid</h3>
                        <span class="badge">8 Months</span>
                        <p>Combine the power of full stack development with AI integration for cutting-edge applications.</p>
                        <ul>
                            <li><i class="fas fa-check-circle"></i> Full curriculum of both programs</li>
                            <li><i class="fas fa-check-circle"></i> AI integration in web applications</li>
                            <li><i class="fas fa-check-circle"></i> Building AI-powered SaaS products</li>
                            <li><i class="fas fa-check-circle"></i> Capstone project mentorship</li>
                            <li><i class="fas fa-check-circle"></i> Extended internship period</li>
                        </ul>
                        <a href="#apply" class="btn">Apply Now</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="benefits" class="features">
        <div class="container">
            <div class="section-title">
                <h2>Internship Benefits</h2>
                <p>Why our internship program stands out from the rest</p>
            </div>
            <div class="feature-grid">
                <div class="feature-card fade-in">
                    <i class="fas fa-user-tie"></i>
                    <h3>1-on-1 Mentorship</h3>
                    <p>Get personalized guidance from senior engineers with 5+ years of industry experience in your chosen field.</p>
                </div>
                <div class="feature-card fade-in delay-1">
                    <i class="fas fa-project-diagram"></i>
                    <h3>Real Projects</h3>
                    <p>Work on actual client projects and build a professional portfolio that showcases your capabilities.</p>
                </div>
                <div class="feature-card fade-in delay-2">
                    <i class="fas fa-money-bill-wave"></i>
                    <h3>Stipends Available</h3>
                    <p>High-performing interns receive monthly stipends based on their progress and project contributions.</p>
                </div>
                <div class="feature-card fade-in delay-3">
                    <i class="fas fa-briefcase"></i>
                    <h3>Job Placement</h3>
                    <p>Top interns receive job offers from our partner companies or get referrals to tech giants.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item fade-in">
                    <h3>96%</h3>
                    <p>Placement Rate</p>
                </div>
                <div class="stat-item fade-in delay-1">
                    <h3>250+</h3>
                    <p>Alumni Network</p>
                </div>
                <div class="stat-item fade-in delay-2">
                    <h3>15+</h3>
                    <p>Partner Companies</p>
                </div>
                <div class="stat-item fade-in delay-3">
                    <h3>4.9★</h3>
                    <p>Average Rating</p>
                </div>
            </div>
        </div>
    </section>

    <section id="testimonials" class="testimonials">
        <div class="container">
            <div class="section-title">
                <h2>What Our Alumni Say</h2>
                <p>Hear from previous interns who transformed their careers</p>
            </div>
            <div class="testimonial-grid">
                <div class="testimonial-card fade-in">
                    <div class="rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p>"The Full Stack internship gave me the confidence to build complete applications. I landed a job at a Series B startup even before graduating!"</p>
                    <div class="author">
                        <img src="https://randomuser.me/api/portraits/women/32.jpg" alt="Sarah K.">
                        <div class="author-info">
                            <h4>Sarah K.</h4>
                            <p>Software Engineer @TechStart</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card fade-in delay-1">
                    <div class="rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p>"The Generative AI program helped me bridge the gap between academic ML and production AI systems. I now lead AI projects at a Fortune 500 company."</p>
                    <div class="author">
                        <img src="https://randomuser.me/api/portraits/men/54.jpg" alt="Raj P.">
                        <div class="author-info">
                            <h4>Raj P.</h4>
                            <p>AI Engineer @DataCorp</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card fade-in delay-2">
                    <div class="rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <p>"The hybrid program was intense but worth it. I could showcase both web dev and AI skills in interviews, resulting in multiple offers from top firms."</p>
                    <div class="author">
                        <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Mia Z.">
                        <div class="author-info">
                            <h4>Mia Z.</h4>
                            <p>Full Stack AI Developer @NovaTech</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="apply" class="apply-section">
        <div class="container">
            <div class="section-title">
                <h2>Apply for Internship</h2>
                <p>Take the first step towards your tech career</p>
            </div>
            <div class="application-form fade-in">
                <form id="internshipForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="tel" id="phone" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="university">University</label>
                            <input type="text" id="university" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="major">Major</label>
                            <input type="text" id="major" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="grad-year">Graduation Year</label>
                            <input type="number" id="grad-year" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="program">Preferred Program</label>
                        <select id="program" class="form-control" required>
                            <option value="">Select a program</option>
                            <option value="full-stack">Full Stack Development</option>
                            <option value="generative-ai">Generative AI Engineering</option>
                            <option value="hybrid">Full Stack + AI Hybrid</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="experience">Technical Background (Languages, Frameworks, etc.)</label>
                        <textarea id="experience" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="projects">Previous Projects or Portfolio (Links if available)</label>
                        <textarea id="projects" class="form-control" rows="4"></textarea>
                    </div>
                    <button type="submit" class="submit-btn">Submit Application</button>
                </form>
            </div>
        </div>
    </section>

    <section id="faq" class="faq-section">
        <div class="container">
            <div class="section-title">
                <h2>Frequently Asked Questions</h2>
                <p>Everything you need to know about our internship program</p>
            </div>
            <div class="faq-grid">
                <div class="faq-card fade-in">
                    <h3>
                        <i class="fas fa-question-circle"></i>
                        Who is eligible for these internships?
                    </h3>
                    <p>Current engineering students (BTech/BE) in Computer Science, IT, or related fields who have completed at least 4 semesters. Exceptional candidates from other disciplines with relevant technical skills may also apply.</p>
                </div>
                <div class="faq-card fade-in delay-1">
                    <h3>
                        <i class="fas fa-question-circle"></i>
                        What is the selection process?
                    </h3>
                    <p>Our process includes: 1) Application review, 2) Technical assessment (coding/problem solving), 3) Technical interview, 4) Culture fit interview. The entire process takes 2-3 weeks.</p>
                </div>
                <div class="faq-card fade-in delay-2">
                    <h3>
                        <i class="fas fa-question-circle"></i>
                        Is prior experience required?
                    </h3>
                    <p>For Full Stack: Basic programming knowledge is required. For Generative AI: Some Python and ML coursework is preferred but not mandatory for exceptional candidates. We assess learning potential.</p>
                </div>
                <div class="faq-card fade-in delay-3">
                    <h3>
                        <i class="fas fa-question-circle"></i>
                        What is the time commitment?
                    </h3>
                    <p>Interns are expected to commit 20-30 hours per week. The program is designed to accommodate academic schedules with flexible hours (including weekends).</p>
                </div>
                <div class="faq-card fade-in">
                    <h3>
                        <i class="fas fa-question-circle"></i>
                        Are these remote or in-person?
                    </h3>
                    <p>Our internships are fully remote with daily standups and weekly 1:1s via video. We may offer optional in-person meetups in major cities periodically.</p>
                </div>
                <div class="faq-card fade-in delay-1">
                    <h3>
                        <i class="fas fa-question-circle"></i>
                        What kind of certificate is provided?
                    </h3>
                    <p>Upon successful completion, you'll receive a verifiable digital certificate with detailed performance metrics, project portfolio, and mentor endorsements.</p>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3>TechGenius</h3>
                    <p>Empowering the next generation of engineering talent through hands-on internships in transformative technologies.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-github"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h3>Programs</h3>
                    <ul class="footer-links">
                        <li><a href="#">Full Stack Development</a></li>
                        <li><a href="#">Generative AI Engineering</a></li>
                        <li><a href="#">Hybrid Program</a></li>
                        <li><a href="#">Corporate Training</a></li>
                        <li><a href="#">University Partnerships</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Company</h3>
                    <ul class="footer-links">
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Team</a></li>
                        <li><a href="#">Partners</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Resources</h3>
                    <ul class="footer-links">
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Webinars</a></li>
                        <li><a href="#">Research Papers</a></li>
                        <li><a href="#">Student Projects</a></li>
                        <li><a href="#">Open Source</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2023 TechGenius Internships. All rights reserved. | <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
            </div>
        </div>
    </footer>

    <div class="modal" id="successModal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <h3>Application Received!</h3>
                <p>Thank you for applying to TechGenius Internships. Our team will review your application and get back to you within 7-10 business days.</p>
                <p>In the meantime, check out our <a href="#">preparation resources</a> to ace the technical interview.</p>
                <button class="btn" id="closeModalBtn">Close</button>
            </div>
        </div>
    </div>

    <script>
        // Mobile menu toggle
        const mobileMenuBtn = document.querySelector('.mobile-menu');
        const navLinks = document.querySelector('.nav-links');

        mobileMenuBtn.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            mobileMenuBtn.innerHTML = navLinks.classList.contains('active') ? 
                '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
        });

        // Close mobile menu when clicking a link
        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('active');
                mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
            });
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if(targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if(targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Form submission
        const form = document.getElementById('internshipForm');
        const modal = document.getElementById('successModal');
        const closeBtns = document.querySelectorAll('.close-btn, #closeModalBtn');

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            
            // Here you would typically send the form data to a server
            // For demonstration, we'll just show the success modal
            
            // Reset form
            form.reset();
            
            // Show success modal
            modal.classList.add('active');
        });

        // Close modal
        closeBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                modal.classList.remove('active');
            });
        });

        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
            if(e.target === modal) {
                modal.classList.remove('active');
            }
        });

        // Animate elements when scrolling
        const animateOnScroll = () => {
            const elements = document.querySelectorAll('.fade-in');
            const windowHeight = window.innerHeight;
            
            elements.forEach(element => {
                const elementPos = element.getBoundingClientRect().top;
                const elementVisible = 100; // px
                
                if(elementPos < windowHeight - elementVisible) {
                    element.classList.add('active');
                }
            });
        };

        // Initial check
        animateOnScroll();

        // Check on scroll
        window.addEventListener('scroll', animateOnScroll);
    </script>
<p style="border-radius: 8px; text-align: center; font-size: 12px; color: #fff; margin-top: 16px;position: fixed; left: 8px; bottom: 8px; z-index: 10; background: rgba(0, 0, 0, 0.8); padding: 4px 8px;">Made with <a href="https://enzostvs-deepsite.hf.space" style="color: #fff;" target="_blank" >DeepSite</a> <img src="https://enzostvs-deepsite.hf.space/logo.svg" alt="DeepSite Logo" style="width: 16px; height: 16px; vertical-align: middle;"></p></body>
</html>