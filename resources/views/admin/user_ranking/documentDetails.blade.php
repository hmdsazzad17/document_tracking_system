@extends('admin.layouts.app')
<style>
    ins {
        background-color: green;
        color: white;
    }
    del {
        background-color: red;
    }
    table.diff-wrapper.diff.diff-html.diff-inline {
        background-color: #e9e9e9;
    }
</style>
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="row">

                <div class="col-md-6 mb-4">
                        <div class="card b-radius--10">
                            <div class="card-body">
                                <h5 class="card-title">{{ $response['title'] }}</h5>
                                <span class="card-text"><?php echo $response['introduction']; ?></span>
                                <span class="card-text"><?php echo $response['facts']; ?></span>
                                <span class="card-text"><?php echo $response['summary']; ?></span>
                                <span class="card-text"><?php echo $response['tags']; ?></span>

                            </div>
                        </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card b-radius--10">
                        <div class="card-body">
                            <?php echo $response['difference'] ;?>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


@endsection

@push('breadcrumb-plugins')
    <button class="btn btn-outline--primary btn-sm modalShow addBtn" data-icon=""><i class="las la-plus"></i> @lang('Add New')</button>
@endpush

@push('style')
    <style>
        .image-upload .thumb .profilePicPreview {
            height: 230px;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict"
            let modal = $('#rankingModal');
            let action = `{{ route('ranking.store') }}`;

            $('.addBtn').on('click', function() {
                modal.find('form').attr('action', action);
                modal.modal('show');
                modal.find('form')[0].reset();
                modal.find('.profilePicPreview').css('backgroundImage', `url(${$(this).data('icon')})`);
            });

            $('.editBtn').on('click', function() {
                let ranking = $(this).data('ranking');
                modal.find('[name=level]').val(ranking.level);
                modal.find('[name=name]').val(ranking.name);
                modal.find('[name=minimum_invest]').val(parseFloat(ranking.minimum_invest).toFixed(2));
                modal.find('[name=team_minimum_invest]').val(parseFloat(ranking.min_referral_invest).toFixed(2));
                modal.find('[name=min_referral]').val(ranking.min_referral);
                modal.find('[name=bonus]').val(parseFloat(ranking.bonus).toFixed(2));
                modal.find('[name=description]').val(ranking.description);
                modal.find('.profilePicPreview').css('backgroundImage', `url(${$(this).data('icon')})`);
                modal.find('.icon').removeClass('required');
                modal.find('[name=icon]').removeAttr('required');

                modal.find('form').attr('action', `${action}/${ranking.id}`);
                modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush
