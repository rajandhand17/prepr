<script>

$(document).ready(function () {
        var language = $('#languageId').val();
        getOrganizations(language);
        getTags(language);
        getSkills(language);
        getCategories(language);
        getUsers();
        getResourceModule(language);
        getLevels(language);
        getDuration(language);
    });

    $("#languageId").change(function () {
        var language = $('#languageId').val();
        $('#organisationId').empty();
        $('#listCategory').empty();
        $('#tag').empty();
        $('#challengeSkills').empty();
        $('#challengeLevels').empty();
        $('#challengeDuration').empty();
        getOrganizations(language);
        getTags(language);
        getSkills(language);
        getCategories(language);
       // getLabs(language);
        getResourceModule(language);
        getLevels(language);
        getDuration(language);
    });

    $("#organisationId").change(function(){
        $('.lab_error').hide();
        $('.resource_module_error').hide();
        $("#resourceModule").empty().trigger('change')
        $('#associativeLab').empty().trigger('change')
    });

    /* This function for select Organization */
    function getOrganizations(language){
        $('#organisationId').select2({    
            placeholder: "Select organization",
            ajax: {
                url: '{{ route('getOrganizations') }}',
                type: 'GET',
                dataType: 'json',
                data: function (params) {
                    return {
                        search: params.term,
                        language: language
                    };
                },
                processResults: function (data) {
                    if(data.status == 'fail'){
                      $('#organisationId').select2("close");
                      $('.org_error').show();
                      $('.org_error').html(data.message);
                    } else {
                        $('.org_error').hide();
                        return {
                          results: data.result
                        };
                    }
                }
            }
        });
    }
    function getUsers(){
        $('#userId').select2({      
            placeholder: "Select User",
            ajax: {
                url: "{{ route('getUsers') }}",
                type: 'GET',
                dataType: 'json',
                data: function (params) {
                    return {
                        search: params.term,
                    };
                },
                processResults: function (data) {
                    if(data.status == 'fail'){
                      $('#userId').select2("close");
                      $('.user_error').show();
                      $('.user_error').html(data.message);
                    } else {
                        $('.user_error').hide();
                        return {
                          results: data.result
                        };
                    }
                }
            }
        });
    }

    /* This function for select categorys */
    function getCategories(language){
        // $("#listCategory").closest(".input-group").siblings(".help-block").text('');
        $('#listCategory').select2({           
            placeholder: "Select category",
            ajax: {
                url: '{{route('getCategories')}}',
                cache: true,
                type: 'GET',
                dataType: 'json',
                data: function (params) {
                    return {
                        search: params.term,
                        language: language,
                        component:'challenge'
                    };
                },
                processResults: function (data) {
                    if(data.status == 'fail'){
                      $('#listCategory').select2("close");
                      $('.category_error').show();
                      $('.category_error').html(data.message);
                    } else {
                        $('.category_error').hide();
                        return {
                          results: data.result
                        };
                    }
                }
            }
        });
    }

    /* This function for select tag */
    function getTags(language){
        $('#tag').select2({
            placeholder: "Select Tag",
            ajax: {
                url: "{{route('getLevels')}}",
                cache: true,
                type: 'GET',
                dataType: 'json',
                data: function (params) {
                    return {
                        search: params.term,
                        language : language
                    };
                },
                processResults: function (data) {
                    if(data.status == 'fail'){
                      $('#tag').select2("close");
                      $('.tag_error').show();
                      $('.tag_error').html(data.message);
                    } else {
                        $('.tag_error').hide();
                        return {
                          results: data.result
                        };
                    }
                }
            }
        });
    }

    // This function get skills
    function getSkills(language){
        $('#Skills').select2({
            placeholder: "Select skill",
            ajax: {
                url: '{{route('getSkills')}}',
                cache: true, 
                width: 'resolve', 
                type: 'GET',
                dataType: 'json',
                data: function (params) {
                    return {
                        search: params.term,
                        language : language
                    };
                },
                processResults: function (data) {
                      if(data.status == 'fail'){
                        $('#challengeSkills').select2("close");
                        $('.skill_error').show();
                        $('.skill_error').html(data.message);
                      } else {
                          $('.skill_error').hide();
                          return {
                            results: data.result
                          };
                      }
                }
            }
        });
    }

    // This function get level
    function getLevels(language){
      $('#challengeLevels').select2({
          placeholder: "Select challenge Level.",
          ajax: {
              url: '{{route('getLevels')}}',
              cache: true, 
              width: 'resolve', 
              type: 'GET',
              dataType: 'json',
              data: function (params) {
                  return {
                      search: params.term,
                      language : language
                  };
              },
              processResults: function (data) {
                    if(data.status == 'fail'){
                      $('#challengeLevels').select2("close");
                      $('.level_error').show();
                      $('.level_error').html(data.message);
                    } else {
                        $('.level_error').hide();
                        return {
                          results: data.result
                        };
                    }
              }
          }
      });
    }
      // This function get duration
    function getDuration(language){
      $('#challengeDuration').select2({
          placeholder: "Select challenge Duration",
          ajax: {
              url: '{{route('getDurations')}}',
              cache: true, 
              width: 'resolve', 
              type: 'GET',
              dataType: 'json',
              data: function (params) {
                  return {
                      search: params.term,
                      language : language
                  };
              },
              processResults: function (data) {
                    if(data.status == 'fail'){
                      $('#challengeDuration').select2("close");
                      $('.duration_error').show();
                      $('.duration_error').html(data.message);
                    } else {
                        $('.duration_error').hide();
                        return {
                          results: data.result
                        };
                    }
              }
          }
      });
    }

     
      // This function get skills
      function getResourceModule(language){
        $('#resourceModule').select2({
            placeholder: "Select resource module",
            ajax: {
                url: '{{route('getResourceModules')}}',
                cache: true, 
                width: 'resolve', 
                type: 'GET',
                dataType: 'json',
                data: function (params) {
                    return {
                        search: params.term,
                        language : language,
                        org_id : $('#organisationId').val(),
                    };
                },
                processResults: function (data) {
                    if(data.status == 'fail'){
                        $('#resourceModule').select2("close");
                        $('.resource_module_error').show();
                        $('.resource_module_error').html(data.message);
                    } else {
                        $('.resource_module_error').hide();
                        return {
                          results: data.result
                        };
                    }
                }
            }
        });
      }
       /* add new social link jquery code */
    $('.add_new_social').click(function () {
        var row_no = $(this).attr('row-no');
        var numItems = $('.social_length').length;
        var html_data = $('#social_row-no_1').html();
        //$('#row_no_'+row_no).find(".button_add_remove").html('');
        var row_no = parseInt(row_no) + 1;
        $(this).attr('row-no', row_no);
        var new_html = '<div class="row form-group" id="social_row-no_' + row_no + '">';
        new_html += html_data;
        new_html += '</div>';
        new_html = new_html.replace("social_length", "social_length social_length"+numItems);
        $('.social_area').append(new_html);
        $('#social_row-no_' + row_no).find(".button_add_remove").html('<button class="btn btn-danger" onclick="removeSocialUrl(' + row_no + ')" type="button" style="margin-top: 20px;">-</button>');
        $('#social_row-no_' + row_no).find(".lab_social").val('');
        $('#social_row-no_' + row_no).find(".social_url").val('');

    });

    function removeSocialUrl(row_no) {
        $('.social_area').find('#social_row-no_' + row_no).remove();
    }

      function initialize() {
            var input = document.getElementById('searchTextField');
            new google.maps.places.Autocomplete(input);

            var autocomplete = new google.maps.places.Autocomplete(input);
            google.maps.event.addListener(autocomplete, 'place_changed', function () {
                var place = autocomplete.getPlace();
                console.log(place);

                document.getElementById('cityLat').value = place.geometry.location.lat();
                document.getElementById('cityLng').value = place.geometry.location.lng();
            });
        }

        google.maps.event.addDomListener(window, 'load', initialize);

</script>