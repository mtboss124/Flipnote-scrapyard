#written by pbsds
#quick and dirty, i may add this to UGO.py in Hatenatools
#
#This converter is licensed under AGPL3
#See http://www.gnu.org/licenses/agpl-3.0.html for more information
from PIL import Image
import sys, numpy as np

def decode(input, output):
	f = open(input, "rb").read()
	len1 = np.fromstring(f[8:12], dtype=np.uint32)[0]
	len2 = np.fromstring(f[12:16], dtype=np.uint32)[0]
	if len2 <> 256*192: return
	print len1, len2
	
	palette = np.fromstring(f[16:16+len1], dtype="<u2")
	imagedata = np.fromstring(f[16+len1:16+len1+len2], dtype=np.uint8)
	
	palette2 = np.zeros(256, dtype=">u4")
	for i in xrange(len1/2):
		#a = (palette[i] >> 15) * 0xFF
		r = (palette[i]       & 0x1f)
		g = (palette[i] >> 5  & 0x1f)
		b = (palette[i] >> 10 & 0x1f)
		r = r<<3 | (r>>2)
		g = g<<3 | (g>>2)
		b = b<<3 | (b>>2)
		palette2[i] = (r<<24) | (g<<16) | (b<<8) | 0xFF
		
	out = np.zeros(256*192, dtype=">u4")#np.uint32)
	out[:] = palette2[imagedata]
	
	pic = out.tostring("C")
	pic = Image.fromstring("RGBA", (256, 192), pic)
	pic.save(output, output[output.rfind(".")+1:])

def encode(input, output):
	image = Image.open(input)
	data = np.array(image.getdata(), dtype=np.uint8)>>3
	
	ppos = 0
	palette = np.zeros(256, dtype="<u2")
	pset = set()#faster
	
	out = np.zeros(256*192, dtype=np.uint8)
	for i in xrange(256*192):
		p = (data[i,2]<<10) | (data[i,1]<<5) | data[i,0]
		if p not in pset:
			if ppos >= 256:
				print "Error. Image has more than 256 different colors, unable to encode!"#easy way out
				return False
			
			palette[ppos] = p
			pset.add(p)
			out[i] = ppos
			ppos += 1
		else:
			out[i] = np.where(palette==p)[0][0]
	
	f = open(output, "wb")
	f.write("UGAR\2\0\0\0")
	f.write(np.array((ppos*2, 256*192), dtype=np.uint32).tostring())
	f.write(palette[:ppos].tostring())
	f.write(out.tostring())
	f.close()
	
if __name__ == "__main__":
	if len(sys.argv) > 1 and sys.argv[1] == "-d":
		decode(sys.argv[2], sys.argv[3])
	elif len(sys.argv) > 1 and sys.argv[1] == "-e":
		encode(sys.argv[2], sys.argv[3])
	else:
		print "format:\n\tnbf.py <-d/-e> <input> <output>"
