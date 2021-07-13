#!/usr/bin/env python3

import sys
import csv

INDENT = '    '

class Parse:

    def __init__(self):
        self.functions = []
        self.trace = []
        self.parsing = False
        self.index = 0
        self.sourcePrefix = ''

    def read(self, filename):
        with open(filename, newline='', encoding='utf-8') as f:
            reader = csv.reader(f, delimiter='\t')
            for r in reader:
                self.trace.append(r)
    
    def _advance(self):
        self.index += 1

    def _enter(self):
        f = self.trace[self.index][5]
        if self.trace[self.index][6] == '1':
            args = ''
            ca = int(self.trace[self.index][10])
            if ca > 0:
                args = '('
                c = ''
                for i in range(ca):
                    args = '{}{}{}'.format(args, c, self.trace[self.index][11 + i])
                    c = ', '
                args += ')'
                if len(args) > 30 and ca > 1:
                    args = args[0:15] + ' ... ' + args[-15:]
            print('{} --> {} @ {}:{} {}'.format(len(self.functions) * INDENT, f,
                                            self.trace[self.index][8][len(self.sourcePrefix):],
                                            self.trace[self.index][9],
                                               args))
        self.functions.append((f, self.index))

    def _exit(self):
        if len(self.functions) > 0:
            f = self.functions.pop()
            retVal = ''
            if len(self.functions) > 0:
                if self.trace[self.index + 1][2] == 'R':
                    temp = '{}'.format(self.trace[self.index + 1][5])
                    if len(temp) > 20:
                        retVal = ' ' + temp[0:20] + '...'
                    else:
                        retVal = ' ' + temp
            print('{} <-- {}{}'.format(len(self.functions) * INDENT, f[0], retVal))

    def _result(self):
        pass

    def parse(self, srcPrefix=''):
        self.sourcePrefix = srcPrefix
        self.index = 0
        self.parsing = True
        count = len(self.trace)
        while self.index < count:
            if len(self.trace[self.index]) == 0:
                self._advance()
                continue
            elif self.trace[self.index][0][0:8] == 'Version:':
                self._advance()
                continue
            elif self.trace[self.index][0][0:12] == 'File format:':
                self._advance()
                continue
            elif self.trace[self.index][0][0:11] == 'TRACE START':
                self._advance()
                continue
            elif self.trace[self.index][0][0:9] == 'TRACE END':
                self._advance()
                continue
            elif self.trace[self.index][2] == '0':
                self._enter()
                self._advance()
            elif self.trace[self.index][2] == '1':
                self._exit()
                self._advance()
            elif self.trace[self.index][2] == 'R':
                self._result()
                self._advance()
            elif self.trace[self.index][2] == '':
                self._advance()
            else:
                print('Unknown row type:')
                print('    "{}"'.format(self.trace[self.index]))
                sys.exit();
        self.parsing = False

FILE = sys.argv[1]
STRIP = '/home/newui/newsledger/'
if len(sys.argv) > 2:
    STRIP = sys.argv[2]

p = Parse()
p.read(FILE)
p.parse(STRIP)

