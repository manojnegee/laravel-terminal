import { Command } from '../command';
export declare class MySQL extends Command {
    is(command: any): boolean;
    isInterpreter(command: string): boolean;
    getInterpreter(): any;
}
