<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Harris Engineer Helpdesk</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Viga&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />

    <style>
        :root {
            --primary: #003366;
            --secondary: #ff6600;
            --light: #f8f9fa;
            --dark: #333333;
        }

        body {
            font-family: "Poppins", sans-serif;
            color: #333;
            line-height: 1.6;
        }

        .navbar-brand {
            font-family: "Viga", sans-serif;
            font-size: 1.5rem;
            color: var(--primary) !important;
            font-weight: bold;
        }

        .navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px 0;
        }

        .nav-link {
            color: var(--dark) !important;
            font-weight: 500;
            margin: 0 10px;
            transition: all 0.3s;
        }

        .nav-link:hover {
            color: var(--secondary) !important;
        }

        .nav-link.active {
            color: var(--secondary) !important;
        }

        .login-button {
            background: var(--secondary);
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .login-button:hover {
            background: #e55a00;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 102, 0, 0.2);
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #003366 0%, #004080 100%);
            color: white;
            padding: 80px 0;
            min-height: 80vh;
            display: flex;
            align-items: center;
        }

        .hero h1 {
            font-family: "Viga", sans-serif;
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero p {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 30px;
        }

        .hero-image {
            max-width: 100%;
            height: auto;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        /* Search Section */
        .search-section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-top: -50px;
            position: relative;
            z-index: 1;
        }

        .search-title {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        .search-form {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .search-input {
            flex: 1;
            padding: 12px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }

        .search-input:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 3px rgba(255, 102, 0, 0.1);
            outline: none;
        }

        .search-button {
            background: var(--secondary);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .search-button:hover {
            background: #e55a00;
            transform: translateY(-2px);
        }

        .create-ticket-button {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .create-ticket-button:hover {
            background: #002244;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 51, 102, 0.2);
        }

        /* Search Results */
        .search-results {
            margin-top: 40px;
            padding: 40px 0;
        }

        .result-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border-left: 4px solid var(--secondary);
        }

        .result-title {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 20px;
            font-size: 1.8rem;
        }

        .result-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 15px;
            color: #333;
            font-weight: 500;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-open {
            background: #e3f2fd;
            color: #1565c0;
        }

        .status-in_progress {
            background: #fff8e1;
            color: #ff8f00;
        }

        .status-completed {
            background: #e8f5e9;
            color: #2e7d32;
        }

        /* Footer */
        .footer {
            background: var(--primary);
            color: white;
            padding: 40px 0 20px;
            margin-top: 60px;
        }

        .footer h5 {
            font-weight: 600;
            margin-bottom: 20px;
            color: white;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            display: block;
            margin-bottom: 10px;
            transition: all 0.3s;
        }

        .footer-links a:hover {
            color: var(--secondary);
            padding-left: 5px;
        }

        .footer-contact {
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
        }

        .footer-contact i {
            margin-right: 10px;
            color: var(--secondary);
        }

        .copyright {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
            margin-top: 30px;
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.2rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .search-form {
                flex-direction: column;
            }

            .search-input,
            .search-button {
                width: 100%;
            }

            .result-info {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 24 24' fill='%23003366'%3E%3Cpath d='M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5'/%3E%3C/svg%3E"
                    alt="Harris Logo" width="40" height="40" class="d-inline-block align-middle me-2" />
                HARRIS ENGINEER HELPDESK
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Create Ticket</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="btn login-button" href="#">Log In</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1>Engineering Issue?</h1>
                    <p>
                        Harris Engineer Helpdesk is ready to assist you! Report
                        maintenance issues, request repairs, or check your ticket status.
                    </p>
                    <div class="mt-4">
                        <a href="#" class="create-ticket-button me-3">
                            <i class="fas fa-plus-circle"></i>
                            Create Ticket
                        </a>
                        <a href="#search" class="btn btn-outline-light">
                            <i class="fas fa-search"></i>
                            Check Status
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='500' height='400' viewBox='0 0 500 400'%3E%3Cpath fill='%23ffffff' fill-opacity='0.1' d='M0,200 Q250,100 500,200 L500,400 L0,400 Z'/%3E%3Cg fill='none' stroke='%23ffffff' stroke-width='2'%3E%3Cpath d='M100,250 Q200,150 300,250'/%3E%3Ccircle cx='150' cy='150' r='20'/%3E%3Ccircle cx='350' cy='150' r='20'/%3E%3Crect x='200' y='100' width='100' height='50' rx='5'/%3E%3C/g%3E%3C/svg%3E"
                        alt="Engineering Illustration" class="hero-image" />
                </div>
            </div>
        </div>
    </section>

    <!-- Search Section -->
    <section id="search" class="search-section">
        <div class="container">
            <h2 class="search-title">Check Your Ticket Status</h2>
            <p class="text-muted mb-4">
                Enter your ticket number to check the current status and updates.
            </p>

            <form id="statusForm" class="search-form">
                <input type="text" id="ticketNumber" class="search-input"
                    placeholder="Enter ticket number (e.g., TK-2024-001)" required />
                <button type="submit" class="search-button">
                    <i class="fas fa-search"></i>
                    Check Status
                </button>
            </form>

            <p class="text-center text-muted">or</p>

            <div class="text-center">
                <a href="#" class="create-ticket-button">
                    <i class="fas fa-chevron-right"></i>
                    Create New Ticket
                </a>
            </div>
        </div>
    </section>

    <!-- Search Results (Hidden by default) -->
    <div id="searchResults" class="search-results" style="display: none">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="result-card">
                        <h2 class="result-title">Ticket Status Details</h2>

                        <div class="result-info">
                            <div class="info-item">
                                <span class="info-label">Ticket Number</span>
                                <span class="info-value" id="resultTicketNumber">TK-2024-001</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Report Date</span>
                                <span class="info-value" id="resultDate">Jan 15, 2024</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Reporter Name</span>
                                <span class="info-value" id="resultReporter">John Doe</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Department</span>
                                <span class="info-value" id="resultDepartment">Housekeeping</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Issue</span>
                                <span class="info-value" id="resultIssue">AC Not Cooling</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Location</span>
                                <span class="info-value" id="resultLocation">Room 305</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Assigned To</span>
                                <span class="info-value" id="resultAssigned">Technician Arif</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Current Status</span>
                                <span class="status-badge status-open" id="resultStatus">Open</span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <span class="info-label">Description</span>
                            <p class="info-value" id="resultDescription">
                                AC unit in room 305 is not cooling properly. Temperature
                                remains at 28°C despite setting to 22°C.
                            </p>
                        </div>

                        <div class="mb-4">
                            <span class="info-label">Engineer Notes</span>
                            <p class="info-value" id="resultNotes">
                                Diagnosed issue: Compressor malfunction. Parts ordered.
                                Estimated completion: Jan 18, 2024.
                            </p>
                        </div>

                        <div class="text-center">
                            <button onclick="resetSearch()" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Back to Search
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5>Harris Engineer Helpdesk</h5>
                    <p style="opacity: 0.8">
                        Engineering support system for Harris Hotel maintenance and repair
                        requests.
                    </p>
                </div>
                <div class="col-lg-4 mb-4">
                    <h5>Quick Links</h5>
                    <div class="footer-links">
                        <a href="#">Home</a>
                        <a href="#">Create Ticket</a>
                        <a href="#">Login</a>
                        <a href="#">Contact Support</a>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <h5>Contact</h5>
                    <div class="footer-contact">
                        <p><i class="fas fa-phone"></i> Ext. 1234 (Engineering Dept)</p>
                        <p><i class="fas fa-envelope"></i> engineer-support@harris.com</p>
                        <p><i class="fas fa-clock"></i> Mon-Fri: 8:00 AM - 5:00 PM</p>
                        <p>
                            <i class="fas fa-building"></i> Harris Hotel Engineering Office
                        </p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="copyright">
                        <p>
                            &copy; 2024 Harris Hotel Engineer Helpdesk System. All rights
                            reserved.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Dummy ticket data
        const dummyTickets = {
            "TK-2024-001": {
                ticketNumber: "TK-2024-001",
                date: "Jan 15, 2024",
                reporter: "John Doe",
                department: "Housekeeping",
                issue: "AC Not Cooling",
                location: "Room 305",
                assigned: "Technician Arif",
                status: "open",
                statusText: "Open",
                description: "AC unit in room 305 is not cooling properly. Temperature remains at 28°C despite setting to 22°C.",
                notes: "Diagnosed issue: Compressor malfunction. Parts ordered. Estimated completion: Jan 18, 2024.",
            },
            "TK-2024-002": {
                ticketNumber: "TK-2024-002",
                date: "Jan 14, 2024",
                reporter: "Jane Smith",
                department: "F&B Department",
                issue: "Leaking Pipe",
                location: "Kitchen Area",
                assigned: "Technician Budi",
                status: "in_progress",
                statusText: "In Progress",
                description: "Water pipe under sink is leaking. Causing water accumulation on kitchen floor.",
                notes: "Pipe replacement in progress. Need to shut off water supply for 1 hour during repair.",
            },
            "TK-2024-003": {
                ticketNumber: "TK-2024-003",
                date: "Jan 13, 2024",
                reporter: "Robert Johnson",
                department: "Front Office",
                issue: "Broken Light",
                location: "Conference Room",
                assigned: "Technician Sari",
                status: "completed",
                statusText: "Completed",
                description: "Main light fixture in conference room is not working.",
                notes: "Light fixture replaced. All lights now functional. Job completed on Jan 14, 2024.",
            },
        };

        // Form submission handler
        document
            .getElementById("statusForm")
            .addEventListener("submit", function(e) {
                e.preventDefault();
                const ticketNumber = document
                    .getElementById("ticketNumber")
                    .value.trim()
                    .toUpperCase();

                if (dummyTickets[ticketNumber]) {
                    // Show results
                    const ticket = dummyTickets[ticketNumber];

                    // Update result fields
                    document.getElementById("resultTicketNumber").textContent =
                        ticket.ticketNumber;
                    document.getElementById("resultDate").textContent = ticket.date;
                    document.getElementById("resultReporter").textContent =
                        ticket.reporter;
                    document.getElementById("resultDepartment").textContent =
                        ticket.department;
                    document.getElementById("resultIssue").textContent = ticket.issue;
                    document.getElementById("resultLocation").textContent =
                        ticket.location;
                    document.getElementById("resultAssigned").textContent =
                        ticket.assigned;
                    document.getElementById("resultStatus").textContent =
                        ticket.statusText;
                    document.getElementById("resultStatus").className =
                        `status-badge status-${ticket.status}`;
                    document.getElementById("resultDescription").textContent =
                        ticket.description;
                    document.getElementById("resultNotes").textContent = ticket.notes;

                    // Show results and scroll to it
                    document.getElementById("searchResults").style.display = "block";
                    document
                        .getElementById("searchResults")
                        .scrollIntoView({
                            behavior: "smooth"
                        });
                } else {
                    // Show error
                    alert(
                        "Ticket not found. Please check your ticket number and try again.\n\nValid ticket numbers for demo:\nTK-2024-001\nTK-2024-002\nTK-2024-003",
                    );
                }
            });

        // Reset search function
        function resetSearch() {
            document.getElementById("searchResults").style.display = "none";
            document.getElementById("ticketNumber").value = "";
            document.getElementById("ticketNumber").focus();
        }

        // Auto-focus on search input
        document.getElementById("ticketNumber").focus();
    </script>
</body>

</html>
