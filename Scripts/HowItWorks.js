document.addEventListener("click", (event) => {

    if (event.target.id === "ProvidersPage") {
        const mainSection = document.querySelector("body main");
        mainSection.innerHTML = `<section class="hero-section">
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
                        <img src="../images/projet1.png" alt="Discover Opportunities">
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
                        <img src="../images/projet2.png" alt="Pitch">
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
                        <img src="../images/projet3.png" alt="Work">
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
                        <img src="../images/projet4.png" alt="Get Paid">
                        <span class="step-tag">Step 04</span>
                    </div>
                </div>
                <div class="step-content">
                    <h2>Deliver & <span class="blue-text">Get Paid</span> <div class="step-circle">4</div></h2>
                    <p>Submit your completed work for review. Once approved, funds are instantly released to your account. Collect great reviews to build your reputation.</p>
                </div>
            </div>
        </section>`;
    }

    if (event.target.id === "ClientPage") {
        const mainSection = document.querySelector("body main");
        mainSection.innerHTML = `<section class="hero-section">
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
                        <img src="../images/projet1.png" alt="Discover">
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
                        <img src="../images/projet2.png" alt="Connect">
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
                        <img src="../images/projet3.png" alt="Escrow">
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
                        <img src="../images/projet4.png" alt="Review">
                        <span class="step-tag">Step 04</span>
                    </div>
                </div>
                <div class="step-content">
                    <h2>Approve & <span class="blue-text">Review</span> <div class="step-circle">4</div></h2>
                    <p>Review the final output. Once you are happy, release the payment and leave a review to help the community grow.</p>
                </div>
            </div>
        </section>`;
    }
});