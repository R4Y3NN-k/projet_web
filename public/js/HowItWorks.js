// This variable will hold the correct Twig paths once we grab them
let secureImgPaths = null;

document.addEventListener("click", (event) => {
    // Only run if they clicked one of our two buttons
    if (event.target.id !== "ProvidersPage" && event.target.id !== "ClientPage") return;

    // STEP A: Before we overwrite any HTML, grab the exact image URLs that Twig successfully loaded!
    if (!secureImgPaths) {
        const currentImages = document.querySelectorAll(".img-box img");
        
        if (currentImages.length >= 4) {
            secureImgPaths = {
                img1: currentImages[0].getAttribute("src"),
                img2: currentImages[1].getAttribute("src"),
                img3: currentImages[2].getAttribute("src"),
                img4: currentImages[3].getAttribute("src")
            };
        } else {
            // Absolute fallback just in case the DOM is missing something
            secureImgPaths = {
                img1: "/img/projet1.png",
                img2: "/img/projet2.png",
                img3: "/img/projet3.png",
                img4: "/img/projet4.png"
            };
        }
    }

    const mainSection = document.querySelector("body main");

    // STEP B: Rewrite the HTML using the securely saved paths
    if (event.target.id === "ProvidersPage") {
        mainSection.innerHTML = `
        <section class="hero-section">
            <h1>Simple. Secure. Global.</h1>
            <h3>Our platform makes hiring and working protected and seamless.</h3>
            <div class="user-toggle">
                <button class="toggle-btn" id="ClientPage">For Clients</button>
                <button class="toggle-btn active" id="ProvidersPage">For Providers</button>
            </div>
        </section>

        <section class="steps-container">
            <div class="step-card">
                <div class="step-visual">
                    <div class="img-box">
                        <img src="${secureImgPaths.img1}" alt="Discover Opportunities">
                        <span class="step-tag">Step 01</span>
                    </div>
                </div>
                <div class="step-content">
                    <h2>Discover <span class="blue-text">Opportunities</span> <div class="step-circle">1</div></h2>
                    <p>Browse through thousands of job postings or let clients come to you. Our matching system connects your specific skills with businesses ready to hire.</p>
                </div>
            </div>

            <div class="step-card reverse">
                <div class="step-visual">
                    <div class="img-box">
                        <img src="${secureImgPaths.img2}" alt="Pitch">
                        <span class="step-tag">Step 02</span>
                    </div>
                </div>
                <div class="step-content">
                    <h2>Connect & <span class="blue-text">Pitch</span> <div class="step-circle">2</div></h2>
                    <p>Chat directly with potential clients to understand their needs. Submit winning proposals and agree on clear milestones and deadlines before starting.</p>
                </div>
            </div>

            <div class="step-card">
                <div class="step-visual">
                    <div class="img-box">
                        <img src="${secureImgPaths.img3}" alt="Work">
                        <span class="step-tag">Step 03</span>
                    </div>
                </div>
                <div class="step-content">
                    <h2>Work with <span class="blue-text">Confidence</span> <div class="step-circle">3</div></h2>
                    <p>Start working knowing the project funds are already secured in our escrow system. Focus on delivering your best work without worrying about chasing invoices.</p>
                </div>
            </div>

            <div class="step-card reverse">
                <div class="step-visual">
                    <div class="img-box">
                        <img src="${secureImgPaths.img4}" alt="Get Paid">
                        <span class="step-tag">Step 04</span>
                    </div>
                </div>
                <div class="step-content">
                    <h2>Deliver & <span class="blue-text">Get Paid</span> <div class="step-circle">4</div></h2>
                    <p>Submit your completed work for review. Once approved, funds are instantly released to your account. Collect great reviews to build your reputation.</p>
                </div>
            </div>
        </section>

        <section class="security-section">
            <div class="sec-header">
                <span class="sec-badge">SECURITY FIRST</span>
                <h2>Your safety is our priority</h2>
            </div>
            <div class="sec-grid">
                <div class="sec-card">
                    <i class="fas fa-id-card"></i>
                    <h4>Identity Check</h4>
                    <p>Multi-step verification for all professional profiles.</p>
                </div>
                <div class="sec-card featured">
                    <i class="fas fa-lock"></i>
                    <h4>Escrow Protection</h4>
                    <p>Funds are held safely until milestones are completed.</p>
                </div>
                <div class="sec-card">
                    <i class="fas fa-gavel"></i>
                    <h4>24/7 Support</h4>
                    <p>Dedicated team to mediate and resolve disputes.</p>
                </div>
            </div>
        </section>`;
    }

    if (event.target.id === "ClientPage") {
        mainSection.innerHTML = `
        <section class="hero-section">
            <h1>Simple. Secure. Global.</h1>
            <h3>Our platform makes hiring and working protected and seamless.</h3>
            <div class="user-toggle">
                <button class="toggle-btn active" id="ClientPage">For Clients</button>
                <button class="toggle-btn" id="ProvidersPage">For Providers</button>
            </div>
        </section>

        <section class="steps-container">
            <div class="step-card">
                <div class="step-visual">
                    <div class="img-box">
                        <img src="${secureImgPaths.img1}" alt="Discover">
                        <span class="step-tag">Step 01</span>
                    </div>
                </div>
                <div class="step-content">
                    <h2>Find your <span class="blue-text">Need</span> <div class="step-circle">1</div></h2>
                    <p>Search through thousands of verified experts or post a specific job request. Our filters help you find the perfect match by skill and budget.</p>
                </div>
            </div>

            <div class="step-card reverse">
                <div class="step-visual">
                    <div class="img-box">
                        <img src="${secureImgPaths.img2}" alt="Connect">
                        <span class="step-tag">Step 02</span>
                    </div>
                </div>
                <div class="step-content">
                    <h2>Connect & <span class="blue-text">Collab</span> <div class="step-circle">2</div></h2>
                    <p>Interview candidates through secure messaging. Review portfolios and set clear milestones before the work begins.</p>
                </div>
            </div>

            <div class="step-card">
                <div class="step-visual">
                    <div class="img-box">
                        <img src="${secureImgPaths.img3}" alt="Escrow">
                        <span class="step-tag">Step 03</span>
                    </div>
                </div>
                <div class="step-content">
                    <h2>Pay with <span class="blue-text">Trust</span> <div class="step-circle">3</div></h2>
                    <p>We hold project funds in a secure escrow account. Funds are only released when you approve the final result.</p>
                </div>
            </div>

            <div class="step-card reverse">
                <div class="step-visual">
                    <div class="img-box">
                        <img src="${secureImgPaths.img4}" alt="Review">
                        <span class="step-tag">Step 04</span>
                    </div>
                </div>
                <div class="step-content">
                    <h2>Approve & <span class="blue-text">Review</span> <div class="step-circle">4</div></h2>
                    <p>Review the final output. Once you are happy, release the payment and leave a review to help the community grow.</p>
                </div>
            </div>
        </section>

        <section class="security-section">
            <div class="sec-header">
                <span class="sec-badge">SECURITY FIRST</span>
                <h2>Your safety is our priority</h2>
            </div>
            <div class="sec-grid">
                <div class="sec-card">
                    <i class="fas fa-id-card"></i>
                    <h4>Identity Check</h4>
                    <p>Multi-step verification for all professional profiles.</p>
                </div>
                <div class="sec-card featured">
                    <i class="fas fa-lock"></i>
                    <h4>Escrow Protection</h4>
                    <p>Funds are held safely until milestones are completed.</p>
                </div>
                <div class="sec-card">
                    <i class="fas fa-gavel"></i>
                    <h4>24/7 Support</h4>
                    <p>Dedicated team to mediate and resolve disputes.</p>
                </div>
            </div>
        </section>`;
    }
});