    </main> <footer class="bg-dark text-white pt-5 pb-4">
        <div class="container">
            <div class="row">

                <div class="col-md-4 mb-4">
                    <h5 class="text-uppercase fw-bold text-active-emphasis">Barangay Information</h5>
                    <hr class="mb-3 mt-0 d-inline-block mx-auto" style="width: 60px; background-color: #5cb85c; height: 2px" />
                    <p class="mb-1">
                        <strong>Barangay Name:</strong> Bacong
                    </p>
                    <p class="mb-1">
                        <strong>Location:</strong> Dumangas, Iloilo
                    </p>
                    <p class="mb-1">
                        <strong>Population:</strong> 1,385 residents
                    </p>
                </div>

                <div class="col-md-4 mb-4">
                    <h5 class="text-uppercase fw-bold text-active-emphasis">Health Center</h5>
                    <hr class="mb-3 mt-0 d-inline-block mx-auto" style="width: 60px; background-color: #5cb85c; height: 2px" />
                    <p class="mb-1">
                        <strong>Name:</strong> Bacong Barangay Health Center
                    </p>
                    <p class="mb-1">
                        <strong>Address:</strong> Bacong, Dumangas
                    </p>
                    <p class="mb-1">
                        <strong>Contact:</strong> (033) 123-4567
                    </p>
                    <p class="mb-1">
                        <strong>Email:</strong> healthcenter@bacong.gov
                    </p>
                </div>

                <div class="col-md-4 mb-4">
                    <h5 class="text-uppercase fw-bold text-active-emphasis">Contact Us</h5>
                    <hr class="mb-3 mt-0 d-inline-block mx-auto" style="width: 60px; background-color: #5cb85c; height: 2px" />
                    <p class="mb-1">
                        <strong>Barangay Hall:</strong> (033) 987-6543
                    </p>
                    <p class="mb-1">
                        <strong>Email:</strong> barangaybacong@gmail.com
                    </p>
                    <p class="mt-2">
                        <a href="https://www.facebook.com/barangay.bacong.2025" target="_blank" class="text-white me-2">Facebook</a> |
                        <a href="https://www.google.com/maps/search/?api=1&query=Barangay+Bacong+Dumangas+Iloilo" target="_blank" class="text-white ms-2">Google Maps</a>
                    </p>
                </div>

            </div>
            
            <hr class="text-white-50">

            <div class="row">
                <div class="col-12 text-center">
                    <p class="mb-0">© 2025 E-BHW Project. All Rights Reserved.</p>
                </div>
            </div>
        </div>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- AOS JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/chatbot.css">
    <script src="<?php echo BASE_URL; ?>assets/js/chatbot.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/resizable.js"></script>

    <div id="chat-bubble" role="button" aria-label="Chat with Gabby" title="Chat with Gabby"></div>

    <div id="chat-window">
        <div id="chat-resize-handle"></div>
        <div id="chat-header">
            <div style="display:flex;align-items:center;gap:.5rem;justify-content:center;">
                <img src="<?php echo BASE_URL; ?>assets/images/gabby_avatar.png" alt="Gabby" style="width:28px;height:28px;border-radius:8px;border:2px solid rgba(255,255,255,0.12);" />
                <div>Gabby — E-BHM Connect</div>
            </div>
            <span id="chat-close">X</span>
        </div>
        <div id="chat-messages">
            <div class="chat-message bot">
                Hi! I'm Gabby. How can I help you today?
            </div>
        </div>
        <div id="chat-input-area">
            <input type="text" id="chat-input" placeholder="Ask a question...">
            <button id="chat-send-btn">→</button>
        </div>
    </div>

    </body>
    </html>
