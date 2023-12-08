import React, {useEffect, useState} from 'react';
import axios from "axios";
import DateTime from "../component/DateTime";

export default function (props) {
    const [comments, setComments] = useState(null);
    const [inputFieldOpened, setInputFieldOpened] = useState(false);
    const [userComment, setUserComment] = useState('');
    const [username, setUsername] = useState('');

    async function fetchComments() {
        const response = await axios.get(`/api/article/${props.articleId}/comments`);
        if (response.status === 200) {
            setComments(response.data);
        }
    }

    useEffect(() => {
        fetchComments().then(_ => console.debug('comments loaded'));
    }, []);

    return <div>
        <h2 className="text-2xl font-bold">Discussion {comments &&
            <span className="text-muted">({comments.length})</span>}</h2>

        <textarea
            name="comment"
            onFocus={() => setInputFieldOpened(true)}
            className="mt-4 p-2 w-full rounded dark:bg-zinc-700 bg-zinc-100"
            value={userComment}
            onChange={e => setUserComment(e.target.value)}
            rows={inputFieldOpened ? 5 : 3}
            placeholder="Participe à la discussion 💬">
        </textarea>

        {inputFieldOpened &&
            <div className="mt-2 mb-4 flex gap-2">
                <input type="text"
                       name="author"
                       value={username}
                       onChange={e => setUsername(e.target.value)}
                       placeholder="Pseudonyme 👻"
                       className="p-2 rounded dark:bg-zinc-700 bg-zinc-100 min-w-0"/>
                <button disabled={userComment.length < 1 || username.length < 1}
                        className="rounded bg-blue-800 disabled:cursor-not-allowed disabled:opacity-50 p-2 text-white font-bold ms-auto">
                    Partager
                </button>
            </div>}

        <ul className="mt-4">
            {comments &&
                comments.map(({id, author, publishAt, content}) => (
                    <li key={id} className="my-4 p-4 rounded border border-zinc-400">
                        <p className="mb-2">
                            <span className="text-lg font-bold">{author}</span>
                            <span className="font-bold text-muted"> • </span>
                            <DateTime className="text-muted" dateTime={publishAt}></DateTime>
                        </p>
                        <p className="text-justify">{content}</p>
                    </li>
                ))}
        </ul>
    </div>;
}
