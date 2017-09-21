var NoteSection = React.createClass({
    getInitialState: function() {
        return {
            notes: []
        }
    },
    componentDidMount: function() {
        this.loadNotesFromServer();
        setInterval(this.loadNotesFromServer, 2000);
    },
    loadNotesFromServer: function() {
        $.ajax({
            url: '/genus/octopus/notes',
            success: function (data) {
                this.setState({notes: data.notes});
            }.bind(this)
        });
    },
    render: function() {
        return (
            <div>
            <div className="notes-container">
            <h2 className="notes-header">Notes</h2>
            <div><i className="fa fa-plus plus-btn"></i></div>
        </div>
        <NoteList notes={this.state.notes} />
        </div>
    );
    }
});

window.NoteSection = NoteSection;