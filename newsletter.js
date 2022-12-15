class Newsletter {

    constructor(options) {
        let defaults = {
            page_id: 1,
            container: null,
            php_file_url: 'subscribe.php',
            type: 'static',
            title: 'Subscribe to our newsletter',
            content: 'Join our subscribers list to get the latest news and updates about books, workshop and more.',
            trigger_position: 200,
            width: null,
            height: null
        };
        this.options = Object.assign(defaults, options);
        if (this.type == 'static') {
            if (this.container == null) {
                this.container = document.createElement('div');
                this.container.classList.add('newsletter');
                document.body.appendChild(this.container);
            }
            if (this.container.innerHTML == '') {
                this.container.innerHTML = `
                <div class="newsletter-container">

                    <h3><i class="fa-regular fa-envelope"></i>${this.title}</h3>

                    <p>${this.content}</p>

                    <form action="${this.phpFileUrl}" method="post">
                        <input type="email" name="email" placeholder="Your Email" required>
                        <button type="submit">Subscribe</button>
                    </form>

                    <p class="newsletter-msg"></p>

                </div>
                `;
            }
        } else if (this.type == 'scroll' || this.type == 'popup') {
            if (this.container == null) {
                this.container = document.createElement('div');
                this.container.classList.add('newsletter-popup');
                document.body.appendChild(this.container);
            }
            if (this.container.innerHTML == '') {
                this.container.innerHTML = `
                <div class="newsletter-container">

                    <a href="#" class="newsletter-close-btn">&times;</a>

                    <h3><i class="fa-regular fa-envelope"></i>${this.title}</h3>

                    <p>${this.content}</p>

                    <form action="${this.phpFileUrl}" method="post">
                        <input type="email" name="email" placeholder="Your Email" required>
                        <button type="submit">Subscribe</button>
                    </form>

                    <p class="newsletter-msg"></p>

                </div>
                `;
            }
        }
        let styleElement = this.type == 'scroll' || this.type == 'popup' ? this.container.querySelector('.newsletter-container') : this.container;
        if (this.options.width != null) {
            styleElement.style.width = this.options.width;
        }
        if (this.options.height != null) {
            styleElement.style.height = this.options.height;
        }
        this._eventHandlers();
    }

    get pageId() {
        return this.options.page_id;
    }

    set pageId(value) {
        this.options.page_id = value;
    }

    get phpFileUrl() {
        return this.options.php_file_url;
    }

    set phpFileUrl(value) {
        this.options.php_file_url = value;
    }

    get container() {
        return this.options.container;
    }

    set container(value) {
        this.options.container = value;
    }

    get type() {
        return this.options.type;
    }

    set type(value) {
        this.options.type = value;
    }

    get title() {
        return this.options.title;
    }

    set title(value) {
        this.options.title = value;
    }

    get content() {
        return this.options.content;
    }

    set content(value) {
        this.options.content = value;
    }

    open() {
        if (this.options.status == 'open' || this.options.status == 'force_closed') return;
        this.options.status = 'open';
        this.container.style.display = 'flex';
        if (this.type != 'static') {
            this.container.getBoundingClientRect();
            this.container.classList.add('open');
            this.container.querySelector('.newsletter-container').getBoundingClientRect();
            this.container.querySelector('.newsletter-container').classList.add('open');
        }
    }

    close(forceClose = false) {
        this.options.status = forceClose ? 'force_closed' : 'closed';
        this.container.style.display = 'none';
        if (this.type != 'static') {
            this.container.classList.remove('open');
            this.container.querySelector('.newsletter-container').classList.remove('open');
        }
    }

    _eventHandlers() {
        let form = this.container.querySelector('form');
        form.onsubmit = event => {
            event.preventDefault();
            let buttonText = this.container.querySelector('form button').innerHTML;
            this.container.querySelector('form button').innerHTML = '<div class="loader"></div>';
            this.container.querySelector('form button').disabled = true;
            fetch(form.action, {
                method: 'POST',
                body: new FormData(form)
            }).then(response => response.text()).then(data => {
                this.container.querySelector('.newsletter-msg').innerHTML = data;
                this.container.querySelector('.newsletter-msg').style.display = 'block';
                this.container.querySelector('form button').innerHTML = buttonText;
                this.container.querySelector('form button').disabled = false;
            });
        };
        if (this.type == 'scroll') {
            document.addEventListener('scroll', () => {
                if (window.scrollY > this.options.trigger_position) {
                    this.open();
                }
            });
        }
        this.container.querySelectorAll('.newsletter-close-btn').forEach(element => {
            element.onclick = event => {
                event.preventDefault();
                this.close(true);
            };
        });
    }

}