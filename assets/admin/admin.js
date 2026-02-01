/**
 * Fancy Scroll Anims - Admin Scripts
 *
 * @package Fancy_Scroll_Anims
 */

(function($) {
	'use strict';

	// Upload handler class.
	class FrameUploader {
		constructor() {
			this.uploadArea = document.getElementById('fsa-upload-area');
			this.fileInput = document.getElementById('fsa-file-input');
			this.progressContainer = document.getElementById('fsa-upload-progress');
			this.progressBar = document.getElementById('fsa-progress-bar');
			this.progressText = document.getElementById('fsa-progress-text');
			this.fileCountText = document.getElementById('fsa-upload-file-count');
			this.errorsContainer = document.getElementById('fsa-upload-errors');
			this.framesGrid = document.getElementById('fsa-frames-grid');
			this.addMoreBtn = document.getElementById('fsa-add-more-btn');

			this.filesToUpload = [];
			this.currentFileIndex = 0;
			this.uploadedCount = 0;
			this.failedCount = 0;

			this.init();
		}

		init() {
			if (!this.fileInput) {
				return;
			}

			// Click to select files.
			if (this.uploadArea) {
				this.uploadArea.addEventListener('click', () => this.fileInput.click());
			}

			if (this.addMoreBtn) {
				this.addMoreBtn.addEventListener('click', () => this.fileInput.click());
			}

			// File input change.
			this.fileInput.addEventListener('change', (e) => this.handleFileSelect(e));

			// Drag and drop.
			if (this.uploadArea) {
				this.uploadArea.addEventListener('dragover', (e) => this.handleDragOver(e));
				this.uploadArea.addEventListener('dragleave', (e) => this.handleDragLeave(e));
				this.uploadArea.addEventListener('drop', (e) => this.handleDrop(e));
			}

			// Delete frame buttons.
			this.attachDeleteHandlers();
		}

		handleDragOver(e) {
			e.preventDefault();
			e.stopPropagation();
			this.uploadArea.classList.add('fsa-drag-over');
		}

		handleDragLeave(e) {
			e.preventDefault();
			e.stopPropagation();
			this.uploadArea.classList.remove('fsa-drag-over');
		}

		handleDrop(e) {
			e.preventDefault();
			e.stopPropagation();
			this.uploadArea.classList.remove('fsa-drag-over');

			const files = Array.from(e.dataTransfer.files);
			this.processFiles(files);
		}

		handleFileSelect(e) {
			const files = Array.from(e.target.files);
			this.processFiles(files);
			e.target.value = ''; // Reset input.
		}

		processFiles(files) {
			// Filter valid image files.
			const validFiles = files.filter(file => {
				const ext = file.name.split('.').pop().toLowerCase();
				return ['webp', 'jpg', 'jpeg', 'png'].includes(ext);
			});

			if (validFiles.length === 0) {
				this.showError(fsaAdmin.strings.invalidFormat);
				return;
			}

			this.filesToUpload = validFiles;
			this.currentFileIndex = 0;
			this.uploadedCount = 0;
			this.failedCount = 0;

			this.startUpload();
		}

		startUpload() {
			// Hide upload area, show progress.
			if (this.uploadArea) {
				this.uploadArea.style.display = 'none';
			}
			this.progressContainer.style.display = 'block';
			this.errorsContainer.style.display = 'none';
			this.errorsContainer.innerHTML = '';

			this.updateProgress();
			this.uploadNextFile();
		}

		uploadNextFile() {
			if (this.currentFileIndex >= this.filesToUpload.length) {
				this.finishUpload();
				return;
			}

			const file = this.filesToUpload[this.currentFileIndex];
			const formData = new FormData();

			formData.append('action', 'fsa_upload_frame');
			formData.append('nonce', fsaAdmin.nonce);
			formData.append('post_id', $('#post_ID').val());
			formData.append('file', file);

			const xhr = new XMLHttpRequest();

			xhr.upload.addEventListener('progress', (e) => {
				if (e.lengthComputable) {
					const fileProgress = (e.loaded / e.total) * 100;
					const totalProgress = ((this.currentFileIndex + (fileProgress / 100)) / this.filesToUpload.length) * 100;
					this.updateProgress(Math.round(totalProgress));
				}
			});

			xhr.addEventListener('load', () => {
				if (xhr.status === 200) {
					try {
						const response = JSON.parse(xhr.responseText);
						if (response.success) {
							this.uploadedCount++;
							this.addFrameToGrid(response.data);
						} else {
							this.failedCount++;
							this.showError(response.data.message || fsaAdmin.strings.uploadError);
						}
					} catch (error) {
						this.failedCount++;
						this.showError(fsaAdmin.strings.uploadError);
					}
				} else {
					this.failedCount++;
					this.showError(fsaAdmin.strings.uploadError);
				}

				this.currentFileIndex++;
				this.uploadNextFile();
			});

			xhr.addEventListener('error', () => {
				this.failedCount++;
				this.showError(fsaAdmin.strings.uploadError);
				this.currentFileIndex++;
				this.uploadNextFile();
			});

			xhr.open('POST', fsaAdmin.ajaxUrl);
			xhr.send(formData);
		}

		updateProgress(percent = null) {
			let progress = percent;

			if (progress === null) {
				progress = Math.round((this.currentFileIndex / this.filesToUpload.length) * 100);
			}

			this.progressBar.value = progress;
			this.progressText.textContent = progress + '%';
			this.fileCountText.textContent = `${this.currentFileIndex} / ${this.filesToUpload.length} files`;
		}

		finishUpload() {
			setTimeout(() => {
				this.progressContainer.style.display = 'none';

				// Reload page to show updated frames grid.
				if (this.uploadedCount > 0) {
					location.reload();
				} else if (this.uploadArea) {
					this.uploadArea.style.display = 'block';
				}
			}, 500);
		}

		addFrameToGrid(frameData) {
			// This is handled by page reload for simplicity.
			// Could be enhanced to add frames dynamically without reload.
		}

		showError(message) {
			this.errorsContainer.style.display = 'block';
			const errorDiv = document.createElement('div');
			errorDiv.className = 'notice notice-error inline';
			errorDiv.innerHTML = `<p>${message}</p>`;
			this.errorsContainer.appendChild(errorDiv);
		}

		attachDeleteHandlers() {
			const deleteButtons = document.querySelectorAll('.fsa-frame-delete');

			deleteButtons.forEach(button => {
				button.addEventListener('click', (e) => {
					e.preventDefault();
					e.stopPropagation();
					this.deleteFrame(button);
				});
			});
		}

		deleteFrame(button) {
			if (!confirm(fsaAdmin.strings.deleteConfirm)) {
				return;
			}

			const frameItem = button.closest('.fsa-frame-item');
			const frameIndex = parseInt(button.dataset.index, 10);

			button.disabled = true;
			button.innerHTML = '<span class="dashicons dashicons-update fsa-spin"></span>';

			$.ajax({
				url: fsaAdmin.ajaxUrl,
				type: 'POST',
				data: {
					action: 'fsa_delete_frame',
					nonce: fsaAdmin.deleteNonce,
					post_id: $('#post_ID').val(),
					frame_index: frameIndex
				},
				success: (response) => {
					if (response.success) {
						frameItem.remove();
						
						// Reload if no frames left.
						if ($('.fsa-frame-item').length === 0) {
							location.reload();
						}
					} else {
						alert(response.data.message || 'Delete failed');
						button.disabled = false;
						button.innerHTML = '<span class="dashicons dashicons-no-alt"></span>';
					}
				},
				error: () => {
					alert('Delete failed');
					button.disabled = false;
					button.innerHTML = '<span class="dashicons dashicons-no-alt"></span>';
				}
			});
		}
	}

	// Preview player class.
	class PreviewPlayer {
		constructor() {
			this.toggleBtn = document.getElementById('fsa-preview-toggle');
			this.framesGrid = document.getElementById('fsa-frames-grid');
			this.frames = [];
			this.currentFrame = 0;
			this.isPlaying = false;
			this.intervalId = null;
			this.fps = 12;
			this.modal = null;
			this.slider = null;
			this.frameImg = null;

			this.init();
		}

		init() {
			if (!this.toggleBtn || !this.framesGrid) {
				return;
			}

			// Collect all frame URLs.
			const frameItems = this.framesGrid.querySelectorAll('.fsa-frame-item');
			frameItems.forEach(item => {
				this.frames.push(item.dataset.url);
			});

			if (this.frames.length === 0) {
				return;
			}

			// Create modal.
			this.createModal();

			// Toggle button click.
			this.toggleBtn.addEventListener('click', () => this.openModal());
		}

		createModal() {
			// Create modal HTML.
			const modal = document.createElement('div');
			modal.className = 'fsa-preview-modal';
			modal.innerHTML = `
				<div class="fsa-preview-content">
					<div class="fsa-preview-header">
						<h3>${fsaAdmin.strings.animationPreview}</h3>
						<button type="button" class="fsa-preview-close">&times;</button>
					</div>
					<div class="fsa-preview-viewport">
						<div class="fsa-preview-animation">
							<img src="${this.frames[0]}" alt="Preview">
						</div>
					</div>
					<div class="fsa-preview-controls">
						<button type="button" class="button button-primary fsa-play-btn">
							<span class="dashicons dashicons-controls-play"></span> ${fsaAdmin.strings.play}
						</button>
						<input type="range" class="fsa-preview-slider" min="0" max="${this.frames.length - 1}" value="0" step="1">
						<div class="fsa-preview-info">
							<span class="fsa-current-frame">1</span> / ${this.frames.length} ${fsaAdmin.strings.frames}
						</div>
					</div>
				</div>
			`;

			document.body.appendChild(modal);
			this.modal = modal;
			this.slider = modal.querySelector('.fsa-preview-slider');
			this.frameImg = modal.querySelector('.fsa-preview-animation img');
			this.playBtn = modal.querySelector('.fsa-play-btn');
			this.frameCounter = modal.querySelector('.fsa-current-frame');

			// Event listeners.
			modal.querySelector('.fsa-preview-close').addEventListener('click', () => this.closeModal());
			modal.addEventListener('click', (e) => {
				if (e.target === modal) {
					this.closeModal();
				}
			});

			this.playBtn.addEventListener('click', () => this.togglePlay());
			this.slider.addEventListener('input', (e) => this.setFrame(parseInt(e.target.value)));
		}

		openModal() {
			this.modal.classList.add('fsa-active');
			this.currentFrame = 0;
			this.updateFrame();
		}

		closeModal() {
			this.modal.classList.remove('fsa-active');
			this.stop();
		}

		togglePlay() {
			if (this.isPlaying) {
				this.stop();
			} else {
				this.play();
			}
		}

		play() {
			this.isPlaying = true;
			this.playBtn.innerHTML = '<span class="dashicons dashicons-controls-pause"></span> ' + fsaAdmin.strings.pause;
			
			this.intervalId = setInterval(() => {
				this.currentFrame = (this.currentFrame + 1) % this.frames.length;
				this.updateFrame();
			}, 1000 / this.fps);
		}

		stop() {
			this.isPlaying = false;
			this.playBtn.innerHTML = '<span class="dashicons dashicons-controls-play"></span> ' + fsaAdmin.strings.play;
			
			if (this.intervalId) {
				clearInterval(this.intervalId);
				this.intervalId = null;
			}
		}

		setFrame(index) {
			this.currentFrame = index;
			this.updateFrame();
			if (this.isPlaying) {
				this.stop();
			}
		}

		updateFrame() {
			this.frameImg.src = this.frames[this.currentFrame];
			this.slider.value = this.currentFrame;
			this.frameCounter.textContent = this.currentFrame + 1;
		}
	}

	// Copy shortcode functionality.
	function initCopyShortcode() {
		const copyBtn = document.getElementById('fsa-copy-shortcode');
		
		if (!copyBtn) {
			return;
		}

		copyBtn.addEventListener('click', function() {
			const shortcode = this.dataset.shortcode;
			
			navigator.clipboard.writeText(shortcode).then(() => {
				const originalText = this.innerHTML;
				this.innerHTML = '<span class="dashicons dashicons-yes" style="margin-top:3px;"></span> ' + fsaAdmin.strings.copied;
				this.style.background = '#d4edda';
				
				setTimeout(() => {
					this.innerHTML = originalText;
					this.style.background = '';
				}, 2000);
			}).catch(() => {
				alert(fsaAdmin.strings.copyFailed);
			});
		});
	}

	// Initialize on DOM ready.
	$(document).ready(function() {
		new FrameUploader();
		new PreviewPlayer();
		initCopyShortcode();
	});

})(jQuery);
