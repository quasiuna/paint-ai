/**
 * Created with the standard prompt, plus:
 * Add a tool that lets me add random emojis
 */

class EmojiTool extends Tool {
    constructor(name) {
        super(name);
        this.name = 'EmojiTool';
        this.description = 'Emojis';
        this.icon = 'fa-smile';
        this.emojiCount = 5;
        this.emojis = this.generateRandomEmojis(this.emojiCount);
    }

    draw(e) {
        if (!this.drawing) return;
        let mousePos = this.getMousePos(this.canvas, e);
        const randomEmoji = this.emojis[Math.floor(Math.random() * this.emojis.length)];
        const fontSize = 32; // can be adjustable
        this.ctx.font = `${fontSize}px Arial`;
        this.ctx.fillText(randomEmoji, mousePos.x - this.canvas.offsetLeft, mousePos.y - this.canvas.offsetTop);
    }

    stopDrawing() {
        super.stopDrawing();
        this.emojis = this.generateRandomEmojis(this.emojiCount);
    }

    getRandomEmoji() {
        const emojiRanges = [
            [0x1F600, 0x1F64F], // Emoticons
            [0x1F300, 0x1F5FF], // Miscellaneous Symbols and Pictographs
            [0x1F680, 0x1F6FF], // Transport and Map Symbols
            [0x1F900, 0x1F9FF], // Supplemental Symbols and Pictographs
            [0x1F600, 0x1F64F], // Smileys & Emotion
            [0x1F400, 0x1F4FF], // People & Body
            [0x1F300, 0x1F3FF], // Animals & Nature
            [0x1F32D, 0x1F37F], // Food & Drink
            [0x1F680, 0x1F6FF], // Travel & Places
            [0x1F7E0, 0x1F7EB], // Activities
            [0x1FA70, 0x1FAFF], // Objects
            [0x2600, 0x26FF],   // Symbols
            [0x1F1E6, 0x1F1FF]  // Flags
        ];
    
        const range = emojiRanges[Math.floor(Math.random() * emojiRanges.length)];
        const codePoint = Math.floor(Math.random() * (range[1] - range[0])) + range[0];
        return String.fromCodePoint(codePoint);
    }
    
    generateRandomEmojis(count) {
        const emojis = [];
        for (let i = 0; i < count; i++) {
            emojis.push(this.getRandomEmoji());
        }
        return emojis;
    }
}

loadPlugin(EmojiTool);
