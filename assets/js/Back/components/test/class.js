export default class Test {

    constructor(entity) {
        this.entity = entity;
    }

    click() {

        // $('#' + this.id).text(this.id);

        $.ajax({

            method: 'POST',
            url: this.entity.attr('action'),
            type: 'POST',
            data: $('#form').val(),
            success: function(data) {
                console.log(data);
            }

        })

    }

}