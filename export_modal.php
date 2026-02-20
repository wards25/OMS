<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fa fa-download fa-sm"></i> Export Data</h6>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><small>×</small></span>
                </button>
            </div>
            <form method="POST" action="export_column.php">
            <div class="modal-body">
                <div id="alert"></div>
                <div class="row">
                    <div class="col-6">
                        <h5>Available fields</h5>
                        <hr>
                    </div>
                    <div class="col-6">
                        <h5>Fields to export</h5>   
                        <hr>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="input-group mb-2">
                            <label class="p-1">From: &nbsp;</label>
                            <div class="input-group-prepend" style="width:60%;">
                                <input type="date" class="form-control form-control-sm" name="from">
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="input-group mb-2">
                            <label class="p-1">To: &nbsp;</label>
                            <div class="input-group-prepend" style="width:60%;">
                                <input type="date" class="form-control form-control-sm" name="to">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Option Table-->
                <div class="row">
                    <div class="col-6">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <tbody id="export-list1">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <tbody id="export-list2">
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                <button class="btn btn-success btn-sm" name="submit" type="submit">Export</button>
            </div>
            </form>
        </div>
    </div>
</div>