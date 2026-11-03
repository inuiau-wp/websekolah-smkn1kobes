{{-- Editor rich-text TinyMCE untuk textarea #content (form berita/modul guru & admin) --}}
<script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js" referrerpolicy="origin"></script>
<script src="https://cdn.jsdelivr.net/npm/marked@12/marked.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        tinymce.init({
            selector: '#content',
            height: 480,
            menubar: 'insert format',
            plugins: [
                'anchor', 'autolink', 'charmap', 'codesample', 'emoticons',
                'image', 'link', 'lists', 'media', 'table', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media table codesample instagram | removeformat',
            codesample_languages: [
                { text: 'HTML/XML', value: 'html' },
                { text: 'CSS', value: 'css' },
                { text: 'PHP', value: 'php' },
                { text: 'JavaScript', value: 'javascript' },
                { text: 'SQL', value: 'sql' }
            ],
            images_file_types: 'jpg,jpeg,png,gif,webp,bmp,avif',
            automatic_uploads: true,
            paste_data_images: true,
            images_upload_handler: function (blobInfo, progress) {
                return new Promise(function (resolve, reject) {
                    var meta = document.querySelector('meta[name="csrf-token"]');
                    var xhr = new XMLHttpRequest();
                    var formData = new FormData();

                    formData.append('file', blobInfo.blob(), blobInfo.filename());
                    xhr.open('POST', "{{ route('news.image-upload.store', absolute: false) }}");
                    if (meta) { xhr.setRequestHeader('X-CSRF-TOKEN', meta.content); }

                    xhr.upload.onprogress = function (e) { if (e.lengthComputable) { progress(e.loaded / e.total * 100); } };
                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            try {
                                resolve(JSON.parse(xhr.responseText).location);
                            } catch (err) {
                                reject('Respons upload gambar tidak valid.');
                            }
                        } else {
                            reject('Upload gambar gagal (HTTP ' + xhr.status + ').');
                        }
                    };
                    xhr.onerror = function () { reject('Upload gambar gagal: kesalahan jaringan.'); };

                    xhr.send(formData);
                });
            },
            convert_urls: false,
            relative_urls: false,
            branding: false,
            promotion: false,
            license_key: 'gpl',
            invalid_styles: 'color background-color max-width max-height',

            // Tombol khusus: Embed post Instagram (Link → blockquote embed yg valid + disetrika server?)
            setup: function (editor) {
                editor.ui.registry.addButton('instagram', {
                    tooltip: 'Embed Post Instagram',
                    icon: 'image-options',
                    onAction: function () {
                        var url = window.prompt('Tempel URL post Instagram (https://www.instagram.com/p/... atau /reel/...):') || '';
                        url = url.trim();
                        if (!url) { return; }
                        if (! /^https:\/\/(www\.)?instagram\.com\/(p|reel|reels)\//i.test(url)) {
                            window.alert('URL Instagram tidak valid. Gunakan link share post/reel yang asli.');
                            return;
                        }
                        editor.insertContent(
                            '<blockquote class="instagram-media" data-instgrm-permalink="' + url + '" style="display:none"></blockquote>'
                        );
                    }
                });
            },
        });

        // Fase 8h-p — File .md dipilih: baca, konversi Markdown → HTML, isi ke editor
        var attachmentInput = document.getElementById('attachment');
        if (attachmentInput) {
            attachmentInput.addEventListener('change', function () {
                var file = this.files && this.files[0];
                var note = document.getElementById('md-preview-note');

                if (!file || ! /\.(md|markdown)$/i.test(file.name)) {
                    if (note) { note.classList.add('hidden'); }
                    return;
                }

                if (tinymce.get('content') && tinymce.get('content').getContent({ format: 'text' }).trim().length > 0) {
                    if (! confirm('Konten saat ini akan DITIMPA dengan isi file ' + file.name + '. Lanjutkan?')) {
                        this.value = ''; // tidak upload file md
                        return;
                    }
                }

                var reader = new FileReader();
                reader.onload = function (e) {
                    var editor = tinymce.get('content');
                    if (editor && typeof marked !== 'undefined') {
                        editor.setContent(marked.parse(e.target.result));
                    }
                    // File md tidak dikirim ke server (isinya sudah dikonsumsi editor)
                    attachmentInput.value = '';
                    if (note) {
                        note.textContent = '\u2713 Isi "' + file.name + '" sudah dikonversi ke editor.';
                        note.classList.remove('hidden');
                    }
                };
                reader.readAsText(file);
            });
        }
    });
</script>
