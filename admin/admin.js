'use strict';
const aside = document.querySelector('aside'), main = document.querySelector('main'), header = document.querySelector('header');
const asideStyle = window.getComputedStyle(aside);
if (localStorage.getItem('admin_menu') == 'closed') {
    aside.classList.add('closed', 'responsive-hidden');
    main.classList.add('full');
    header.classList.add('full');
}
document.querySelector('.responsive-toggle').onclick = event => {
    event.preventDefault();
    if (asideStyle.display == 'none') {
        aside.classList.remove('closed', 'responsive-hidden');
        main.classList.remove('full');
        header.classList.remove('full');
        localStorage.setItem('admin_menu', '');
    } else {
        aside.classList.add('closed', 'responsive-hidden');
        main.classList.add('full');
        header.classList.add('full');
        localStorage.setItem('admin_menu', 'closed');
    }
};
document.querySelectorAll('.tabs a').forEach((element, index) => {
    element.onclick = event => {
        event.preventDefault();
        document.querySelectorAll('.tabs a').forEach(element => element.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach((element2, index2) => {
            if (index == index2) {
                element.classList.add('active');
                element2.style.display = 'block';
            } else {
                element2.style.display = 'none';
            }
        });
    };
});
if (document.querySelector('.filters a')) {
    let filtersList = document.querySelector('.filters .list');
    let filtersListStyle = window.getComputedStyle(filtersList);
    document.querySelector('.filters a').onclick = event => {
        event.preventDefault();
        if (filtersListStyle.display == 'none') {
            filtersList.style.display = 'flex';
        } else {
            filtersList.style.display = 'none';
        }
    };
    document.onclick = event => {
        if (!event.target.closest('.filters')) {
            filtersList.style.display = 'none';
        }
    };
}
document.querySelectorAll('.msg').forEach(element => {
    element.querySelector('.fa-times').onclick = () => {
        element.remove();
        history.replaceState && history.replaceState(null, '', location.pathname + location.search.replace(/[\?&]success_msg=[^&]+/, '').replace(/^&/, '?') + location.hash);
        history.replaceState && history.replaceState(null, '', location.pathname + location.search.replace(/[\?&]error_msg=[^&]+/, '').replace(/^&/, '?') + location.hash);
    };
});
if (location.search.includes('success_msg') || location.search.includes('error_msg')) {
    history.replaceState && history.replaceState(null, '', location.pathname + location.search.replace(/[\?&]success_msg=[^&]+/, '').replace(/^&/, '?') + location.hash);
    history.replaceState && history.replaceState(null, '', location.pathname + location.search.replace(/[\?&]error_msg=[^&]+/, '').replace(/^&/, '?') + location.hash);
}
document.body.addEventListener('click', event => {
    if (!event.target.closest('.multiselect')) {
        document.querySelectorAll('.multiselect .list').forEach(element => element.style.display = 'none');
    } 
});
document.querySelectorAll('.multiselect').forEach(element => {
    let updateList = () => {
        element.querySelectorAll('.item').forEach(item => {
            element.querySelectorAll('.list span').forEach(newItem => {
                if (item.dataset.value == newItem.dataset.value) {
                    newItem.style.display = 'none';
                }
            });
            item.querySelector('.remove').onclick = () => {
                element.querySelector('.list span[data-value="' + item.dataset.value + '"]').style.display = 'flex';
                item.querySelector('.remove').parentElement.remove();
            };
        });
        if (element.querySelectorAll('.item').length > 0) {
            element.querySelector('.search').placeholder = '';
        }
    };
    element.onclick = () => element.querySelector('.search').focus();
    element.querySelector('.search').onfocus = () => element.querySelector('.list').style.display = 'flex';
    element.querySelector('.search').onclick = () => element.querySelector('.list').style.display = 'flex';
    element.querySelector('.search').onkeyup = () => {
        element.querySelector('.list').style.display = 'flex';
        element.querySelectorAll('.list span').forEach(item => {
            item.style.display = item.innerText.toLowerCase().includes(element.querySelector('.search').value.toLowerCase()) ? 'flex' : 'none';
        });
        updateList();
    };
    element.querySelectorAll('.list span').forEach(item => item.onclick = () => {
        item.style.display = 'none';
        let html = `
            <span class="item" data-value="${item.dataset.value}">
                <i class="remove">&times;</i>${item.innerText}
                <input type="hidden" name="${element.dataset.name}" value="${item.dataset.value}">    
            </span>
        `;
        if (element.querySelector('.item')) {
            let ele = element.querySelectorAll('.item');
            ele = ele[ele.length-1];
            ele.insertAdjacentHTML('afterend', html);                          
        } else {
            element.insertAdjacentHTML('afterbegin', html);
        }
        element.querySelector('.search').value = '';
        updateList();
    });
    updateList();
});
const modal = options => {
    let element;
    if (document.querySelector(options.element)) {
        element = document.querySelector(options.element);
    } else if (options.modalTemplate) {
        document.body.insertAdjacentHTML('beforeend', options.modalTemplate());
        element = document.body.lastElementChild;
    }
    options.element = element;
    options.open = obj => {
        element.style.display = 'flex';
        element.getBoundingClientRect();
        element.classList.add('open');
        if (options.onOpen) options.onOpen(obj);
    };
    options.close = obj => {
        if (options.onClose) {
            let returnCloseValue = options.onClose(obj);
            if (returnCloseValue !== false) {
                element.style.display = 'none';
                element.classList.remove('open');
                element.remove();
            }
        } else {
            element.style.display = 'none';
            element.classList.remove('open');
            element.remove();
        }
    };
    if (options.state == 'close') {
        options.close({ source: element, button: null });
    } else if (options.state == 'open') {
        options.open({ source: element }); 
    }
    element.querySelectorAll('.dialog-close').forEach(e => {
        e.onclick = event => {
            event.preventDefault();
            options.close({ source: element, button: e });
        };
    });
    return options;
};
function insertTextAtCursor(el, text, minus = 0) {
    var val = el.value, endIndex, range;
    if (typeof el.selectionStart != "undefined" && typeof el.selectionEnd != "undefined") {
        endIndex = el.selectionEnd;
        el.value = val.slice(0, el.selectionStart) + text + val.slice(endIndex);
        el.selectionStart = el.selectionEnd = endIndex + text.length - minus;
    } else if (typeof document.selection != "undefined" && typeof document.selection.createRange != "undefined") {
        el.focus();
        range = document.selection.createRange();
        range.collapse(false);
        range.text = text;
        range.select();
    }
}
if (document.querySelector('.format-btn')) {
    document.querySelector('.format-btn.div').onclick = event => {
        event.preventDefault();
        insertTextAtCursor(document.querySelector('#content'), '<div></div>');
    };
    document.querySelector('.format-btn.heading').onclick = event => {
        event.preventDefault();
        insertTextAtCursor(document.querySelector('#content'), '<h1></h1>');
    };
    document.querySelector('.format-btn.paragraph').onclick = event => {
        event.preventDefault();
        insertTextAtCursor(document.querySelector('#content'), '<p></p>');
    };
    document.querySelector('.format-btn.strong').onclick = event => {
        event.preventDefault();
        insertTextAtCursor(document.querySelector('#content'), '<strong></strong>');
    };
    document.querySelector('.format-btn.italic').onclick = event => {
        event.preventDefault();
        insertTextAtCursor(document.querySelector('#content'), '<i></i>');
    };
    document.querySelector('.format-btn.image').onclick = event => {
        event.preventDefault();
        insertTextAtCursor(document.querySelector('#content'), '<img src="" width="" height="" alt="">');
    };
    document.getElementById('content').onkeydown = function(event) {
        if (event.key == 'Tab') {
            let start = this.selectionStart, end = this.selectionEnd, target = event.target, value = target.value;
            target.value = value.substring(0, start) + "\t" + value.substring(end);
            this.selectionStart = this.selectionEnd = start + 1;
            event.preventDefault();
        }
    };
    document.querySelector('.preview-btn a').onclick = event => {
        event.preventDefault();
        let content = document.getElementById('content').value;
        if (content == '') {
            document.getElementById('content').focus();
        } else {
            modal({
                state: 'open',
                modalTemplate: function() {
                    return `
                    <div class="dialog large">
                        <div class="content">
                            <h3 class="heading">Preview<span class="dialog-close">&times;</span></h3>
                            <div class="body">
                                <iframe class="dcontent" style="border:7px solid #fff;width:100%;height:400px;"></iframe>
                            </div>
                            <div class="footer pad-5">
                                <a href="#" class="btn dialog-close">Close</a>
                            </div>
                        </div>
                    </div>
                    `;
                },
                onOpen: function() {
                    document.querySelector('.dcontent').srcdoc = content;
                }
            });    
        }    
    };
}
const recipientErrorHandler = () => {
    document.querySelectorAll('.recipient-error').forEach(element => {
        element.onclick = event => {
            event.preventDefault();
            modal({
                state: 'open',
                modalTemplate: function() {
                    return `
                    <div class="dialog">
                        <div class="content">
                            <h3 class="heading">View Error Message<span class="dialog-close">&times;</span></h3>
                            <div class="body">
                                <p style="padding:10px 20px">${element.title}</p>
                            </div>
                            <div class="footer pad-5">
                                <a href="#" class="btn dialog-close">Close</a>
                            </div>
                        </div>
                    </div>
                    `;
                }
            }); 
        };
    });
};
recipientErrorHandler();
const statusHandler = () => {
    document.querySelectorAll('.status').forEach(element => {
        element.querySelectorAll('.dropdown a').forEach(link => link.onclick = event => {
            event.preventDefault();
            fetch(`campaigns.php?update=${element.dataset.id}&status=${link.dataset.value}`, { cache: 'no-store' }).then(response => response.text()).then(data => {
                if (data.includes('success')) {
                    element.querySelector('span').className = link.dataset.value.toLowerCase();
                    element.querySelector('span').title = link.dataset.value;
                }
            });
        });
    });
};
statusHandler();
document.querySelectorAll('.multi-checkbox').forEach(element => {
    element.querySelector('.check-all input[type="checkbox"]').onclick = event => {
        element.querySelectorAll('.con input[type="checkbox"]').forEach(element => element.checked = event.target.checked ? true : false);
    };
    element.querySelector('.check-all input[type="text"]').onkeyup = event => {
        element.querySelectorAll('.con .item').forEach(item => {
            item.style.display = item.innerText.toLowerCase().includes(element.querySelector('.check-all input[type="text"]').value.toLowerCase()) ? 'flex' : 'none';
        });
    };
});
if (document.querySelector('.ajax-update') && ajax_updates) {
    setInterval(() => {
        fetch(window.location.href, { cache: 'no-store' }).then(response => response.text()).then(data => {
            let doc = (new DOMParser()).parseFromString(data, 'text/html');
            for (let i = 0; i < document.querySelectorAll('.ajax-update').length; i++) {
                document.querySelectorAll('.ajax-update')[i].innerHTML = doc.querySelectorAll('.ajax-update')[i].innerHTML;
            }
            statusHandler();
            recipientErrorHandler();
        });
    }, ajax_interval); 
}
if (document.querySelector('.send-mail-form')) {
    document.querySelector('.send-mail-form').onsubmit = event => {
        event.preventDefault();
        let recipients = [], recipientIndex = 0, recipientsCompleted = 0;
        document.querySelectorAll('input[type="checkbox"]:checked').forEach(item => {
            if (item.id != 'check-all') {
                recipients.push(item.value);
            }
        });
        recipients.push(...document.querySelector('input[name="additional_recipients"]').value.split(','));
        recipients = recipients.filter(n => n);
        modal({
            state: 'open',
            modalTemplate: function() {
                return `
                <div class="dialog send-mail-modal">
                    <div class="content">
                        <h3 class="heading">Sending Mail</h3>
                        <div class="body">
                            <p style="padding:10px 20px;text-align:center">Please wait while we send all the mail...</p>
                            <div class="loader"></div>
                            <p class="num-recipients" style="padding:10px 20px;text-align:center;font-size:18px;">0/${recipients.length} Recipients</p>
                        </div>
                        <div class="footer pad-5" style="display:none">
                            <a href="#" class="btn dialog-close">Close</a>
                        </div>
                    </div>
                </div>
                `;
            },
            onClose: function() {
                document.querySelector('.send-mail-form').reset();
            }
        });
        setInterval(() => {
            if (recipients[recipientIndex]) {
                let formData = new FormData(document.querySelector('.send-mail-form'));
                formData.append('recipient', recipients[recipientIndex]);
                fetch('sendmail.php', { 
                    cache: 'no-store',
                    method: 'POST',
                    body:  formData
                }).then(response => response.text()).then(data => {
                    recipientsCompleted++;
                    document.querySelector('.num-recipients').innerHTML = `${recipientsCompleted}/${recipients.length} Recipients`;
                    if (recipientsCompleted == recipients.length) {
                        let el = document.createElement('p');
                        el.innerHTML = 'Finished!';
                        el.style.cssText = 'font-size:18px;text-align:center;';
                        document.querySelector('.send-mail-modal .loader').replaceWith(el);
                        document.querySelector('.send-mail-modal .footer').style.display = 'block';
                    }
                });
                recipientIndex++;
            }
        }, ajax_interval); 
    };
}