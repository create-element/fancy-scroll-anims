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

	// Initialize on DOM ready.
	$(document).ready(function() {
		new FrameUploader();
	});

})(jQuery);
