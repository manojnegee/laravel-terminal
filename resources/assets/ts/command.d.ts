import { HttpClient } from './httpclient';
export abstract class Command {
    protected client: HttpClient;
    protected options: any;
    constructor(client?: HttpClient, options?: any);
    abstract is(command: string): boolean;
    isInterpreter(command: string): boolean;
    getInterpreter(): any;
    run(command: string): Promise<any>;
    protected parseSentence(command: any): any;
}
