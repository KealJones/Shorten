    <div class="box">
        <h1>Create a ShortURL</h1>
        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>
<div class="portlet light bordered">
									<div class="portlet-title">
										<div class="caption">
											<i class="icon-energy text-primary"></i>
											<span class="caption-subject text-primary bold uppercase">Create ShortURL</span>
										</div>
									</div>
									<div class="portlet-body form" style="display: block;">
										<!-- BEGIN FORM-->
            <form method="post" action="<?php echo Config::get('URL'); ?>short/create">
                											<div class="form-body">
												<h3 class="form-section">URL Shortener!</h3>
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label class="control-label col-md-3">URL</label>
															<div class="col-md-9">
															    <div class="input-group">
															        <span class="input-group-addon">
															            <i class="icon-link"></i>
															        </span>
															        <input name="url" type="text" class="form-control" placeholder="URL to shorten">
															    </div>
																																<span class="help-block">
																Where do we need to go?</span>
															</div>
														</div>
													</div>
													</div>
													</div>
<button type="submit" class="btn green">Submit</button>
            </form>
            </div>
            </div>
            </div>
